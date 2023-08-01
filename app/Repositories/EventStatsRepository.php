<?php

namespace App\Repositories;

use App\Http\Resources\MerchSaleResource;
use App\Models\Donation;
use App\Models\Follower;
use App\Models\MerchSale;
use App\Models\Subscriber;
use App\Models\User;
use App\Modules\Payments\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventStatsRepository
{
    /**
     * @var User $user
     */
    protected User $user;

    /**
     * @var int $ttl
     */
    protected int $ttl;

    /**
     * Set user
     * @param User $user
     * @return EventStatsRepository
     */
    public function setUser(User $user): static
    {
      $this->user = $user;

      return $this;
    }

    /**
     * Set ttl
     * @param int $ttl
     * @return EventStatsRepository
     */
    public function setTTL(int $ttl): static
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Returns total revenue for given user
     * @param int $days
     * @param array $subscriptionTierPrice
     * @return array
     */
    public function getTotalRevenue(int $days, array $subscriptionTierPrice): array
    {
        $key = 'stats:tr:' . $this->user->id;

        if(Cache::has($key)) {
            $total = Cache::get($key);
        } else {
            $total = Donation::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->sum('amount_usd');

            $total += MerchSale::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->sum(DB::raw('price * amount'));

            $total += Subscriber::where('user_id', $this->user->id)
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
                    ->where('tier_id', Subscriber::TIER_1)
                    ->count() * $subscriptionTierPrice[Subscriber::TIER_1];

            $total += Subscriber::where('user_id', $this->user->id)
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
                    ->where('tier_id', Subscriber::TIER_2)
                    ->count() * $subscriptionTierPrice[Subscriber::TIER_2];

            $total += Subscriber::where('user_id', $this->user->id)
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
                    ->where('tier_id', Subscriber::TIER_3)
                    ->count() * $subscriptionTierPrice[Subscriber::TIER_3];

            if($total) {
                Cache::tags('events:' . $this->user->id)->put($key, $total, $this->ttl);
            }
        }

        return [
            'amount' => number_format($total, 2),
            'currency' => Currency::USD
        ];
    }

    /**
     * Returns total number of followers
     * @param int $days
     * @return int
     */
    public function getTotalFollowers(int $days): int
    {
        $key = 'stats:tf:' . $this->user->id;

        if(Cache::has($key)) {
            $total = Cache::get($key);
        } else {
            $total = Follower::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->count();

            if($total) {
                Cache::tags('events:' . $this->user->id)->put($key, $total, $this->ttl);
            }
        }

        return $total;
    }

    /**
     * Returns best merchant sale
     * @param int $days
     * @return mixed
     */
    public function getBestMerchSales(int $days): mixed
    {
        $key = 'stats:bms:' . $this->user->id;

        if(Cache::has($key)) {
            $result = Cache::get($key);
        } else {
            $result = MerchSale::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->orderBy('amount', 'desc')
                ->take(3)
                ->get();

            $result = MerchSaleResource::collection($result);
        }

        return $result;
    }
}
