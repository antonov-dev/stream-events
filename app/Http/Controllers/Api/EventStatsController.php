<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Repositories\EventStatsRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventStatsController extends Controller
{
    use ApiResponse;

    /**
     * @var EventStatsRepository
     */
    protected EventStatsRepository $stats;

    public function __construct(EventStatsRepository $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Return Stats info
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $days = 30;
        $subscriptionTierPrice = [
            Subscriber::TIER_1 => 5,
            Subscriber::TIER_2 => 10,
            Subscriber::TIER_3 => 15,
        ];

        $this->stats->setUser($request->user())
            ->setTTL(5 * 60);
        return $this->success([
            'total_revenue' => $this->stats->getTotalRevenue($days, $subscriptionTierPrice),
            'total_followers' => $this->stats->getTotalFollowers($days),
            'best_merch_sales' => $this->stats->getBestMerchSales($days)
        ]);
    }
}
