<?php

namespace Unit\Repositories;

use App\Http\Resources\EventResource;
use App\Models\User;
use App\Repositories\EventRepository;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected User $user;

    /**
     * @var User
     */
    protected User $user2;


    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user2 = User::factory()->create();

        // Generate new events for given user
        (new EventSeeder())->run($this->user->id, 10);

        // Generate events for another user
        (new EventSeeder())->run($this->user2->id, 10);
    }

    /**
     * @test
     */
    public function user_can_get_list_of_events(): void
    {
        $list = (new EventRepository())
            ->setTTL(0)
            ->setUser($this->user)
            ->getList(0, 10);

        $this->assertCount(10, $list);
        $this->assertInstanceOf(EventResource::class, $list[0]);

        $item = json_decode($list[0]->toJson(), true);
        $nextItem = json_decode($list[1]->toJson(), true);

        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('type', $item);
        $this->assertArrayHasKey('is_read', $item);
        $this->assertArrayHasKey('created_at', $item);

        $this->assertNotEmpty($item['eventable']);
        $this->assertEquals($item['user_id'], $this->user->id);

        $this->assertGreaterThan($item['created_timestamp'], $nextItem['created_timestamp']);
    }

    /**
     * @test
     */
    public function user_can_access_only_his_list_of_events(): void
    {
        $list = (new EventRepository())
            ->setTTL(0)
            ->setUser($this->user)
            ->getList(0, 50);

        $this->assertCount(40, $list);
    }

    /**
     * @test
     */
    public function user_can_get_next_portion_of_events(): void
    {
        $repository = new EventRepository();

        $list = $repository
            ->setTTL(0)
            ->setUser($this->user)
            ->getList(0, 10);
        $last = json_decode($list[array_key_last($list)]->toJson(), true);

        $list = $repository->getList($last['created_timestamp'], 10);
        $first = json_decode($list[0]->toJson(), true);

        $this->assertGreaterThan($last['created_timestamp'], $first['created_timestamp']);
    }

    /**
     * @test
     */
    public function user_can_update_his_event(): void
    {
        $repository = (new EventRepository())
            ->setTTL(0)
            ->setUser($this->user);

        $list = $repository->getList(0, 10);
        $item1 = json_decode($list[0]->toJson(), true);

        $repository->update($item1['id'], ['is_read' => !$item1['is_read']]);

        $list = $repository->getList(0, 10);
        $item2 = json_decode($list[0]->toJson(), true);

        $this->assertNotEquals($item1['is_read'], $item2['is_read']);
    }

    /**
     * @test
     */
    public function user_cant_update_strangers_event(): void
    {
        $repository = (new EventRepository())
            ->setTTL(0)
            ->setUser($this->user);

        $list = $repository->getList(0, 10);
        $item1 = json_decode($list[0]->toJson(), true);

        $repository
            ->setUser($this->user2)
            ->update($item1['id'], ['is_read' => !$item1['is_read']]);

        $list = $repository->setUser($this->user)->getList(0, 10);
        $item2 = json_decode($list[0]->toJson(), true);

        $this->assertEquals($item1['is_read'], $item2['is_read']);
    }
}
