<?php

namespace Feature;

use App\Models\Donation;
use App\Models\Event;
use App\Models\Follower;
use App\Models\MerchSale;
use App\Models\Subscriber;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventsControllerTest extends TestCase
{
    use RefreshDatabase;

    // Index

    /**
     * @test
     */
    public function stranger_cant_get_event_list(): void
    {
        $this->json('get', '/api/events')
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function user_can_get_event_list_without_params(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->json('get', '/api/events')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_can_get_list_of_events(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Generate events for another user
        (new EventSeeder())->run($user->id, 10);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data'])
            ->assertJsonCount(10, 'data');
    }

    /**
     * @test
     */
    public function user_cant_get_strangers_list_of_events(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Generate events for another user
        (new EventSeeder())->run($user->id + 1, 10);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['status'])
            ->assertJsonMissing(['data']);
    }

    /**
     * @test
     */
    public function user_can_get_list_of_event_with_donation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $eventable = Donation::factory()->create(['user_id' => $user->id]);
        $event = Event::create([
            'eventable_type' => Donation::class,
            'eventable_id' => $eventable->id,
            'user_id' => $eventable->user_id,
            'created_at' => $eventable->created_at,
            'updated_at' => $eventable->updated_at,
            'is_read' => false
        ]);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $event->id,
                        'is_read' => $event->is_read,
                        'type' => 'donation',
                        'user_id' => $event->user_id,
                        'created_at' => $event->created_at->toDateTimeString(),
                        'created_timestamp' => $event->created_at->timestamp,
                        'eventable' => [
                            'amount' => $eventable->amount,
                            'currency' => $eventable->currency,
                            'message' => $eventable->message,
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function user_can_get_list_of_event_with_follower(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $eventable = Follower::factory()->create(['user_id' => $user->id]);
        $event = Event::create([
            'eventable_type' => Follower::class,
            'eventable_id' => $eventable->id,
            'user_id' => $eventable->user_id,
            'created_at' => $eventable->created_at,
            'updated_at' => $eventable->updated_at,
            'is_read' => false
        ]);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $event->id,
                        'is_read' => $event->is_read,
                        'type' => 'follower',
                        'user_id' => $event->user_id,
                        'created_at' => $event->created_at->toDateTimeString(),
                        'created_timestamp' => $event->created_at->timestamp,
                        'eventable' => [
                            'name' => $eventable->name
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function user_can_get_list_of_event_with_merch_sale(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $eventable = MerchSale::factory()->create(['user_id' => $user->id]);
        $event = Event::create([
            'eventable_type' => MerchSale::class,
            'eventable_id' => $eventable->id,
            'user_id' => $eventable->user_id,
            'created_at' => $eventable->created_at,
            'updated_at' => $eventable->updated_at,
            'is_read' => false
        ]);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $event->id,
                        'is_read' => $event->is_read,
                        'type' => 'merch_sale',
                        'user_id' => $event->user_id,
                        'created_at' => $event->created_at->toDateTimeString(),
                        'created_timestamp' => $event->created_at->timestamp,
                        'eventable' => [
                            'name' => $eventable->name,
                            'amount' => $eventable->amount,
                            'price' => $eventable->price,
                            'currency' => $eventable->currency,
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function user_can_get_list_of_event_with_subscriber(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $eventable = Subscriber::factory()->create(['user_id' => $user->id]);
        $event = Event::create([
            'eventable_type' => Subscriber::class,
            'eventable_id' => $eventable->id,
            'user_id' => $eventable->user_id,
            'created_at' => $eventable->created_at,
            'updated_at' => $eventable->updated_at,
            'is_read' => false
        ]);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $event->id,
                        'is_read' => $event->is_read,
                        'type' => 'subscriber',
                        'user_id' => $event->user_id,
                        'created_at' => $event->created_at->toDateTimeString(),
                        'created_timestamp' => $event->created_at->timestamp,
                        'eventable' => [
                            'name' => $eventable->name,
                            'tier' => 'Tier ' . $eventable->tier_id
                        ]
                    ]
                ]
            ]);
    }

    // Update

    /**
     * @test
     */
    public function stranger_cant_update_event(): void
    {
        $this->json('patch', '/api/events/1')
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function user_cant_update_event_without_params(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->json('patch', '/api/events/1')
            ->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_cant_update_non_existing_event(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->json('patch', '/api/events/1', [
            'is_read' => true
        ])->assertStatus(404);
    }

    /**
     * @test
     */
    public function user_can_update_his_event(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $eventable = Subscriber::factory()->create(['user_id' => $user->id]);
        $event = Event::create([
            'eventable_type' => Subscriber::class,
            'eventable_id' => $eventable->id,
            'user_id' => $eventable->user_id,
            'created_at' => $eventable->created_at,
            'updated_at' => $eventable->updated_at,
            'is_read' => false
        ]);

        $response = $this->json('patch', '/api/events/'.$event->id, [
            'is_read' => true
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);

        $response = $this->json('get', '/api/events', [
            'last' => 0,
            'limit' => 10
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $event->id,
                        'is_read' => true,
                    ]
                ]
            ]);
    }
}
