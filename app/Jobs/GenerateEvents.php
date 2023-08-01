<?php

namespace App\Jobs;

use App\Models\Donation;
use App\Models\Follower;
use App\Models\MerchSale;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate new events for given user
        Donation::factory()->count(500)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $this->user->id]);

        Follower::factory()->count(500)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $this->user->id]);

        MerchSale::factory()->count(500)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $this->user->id]);

        Subscriber::factory()->count(500)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $this->user->id]);
    }
}
