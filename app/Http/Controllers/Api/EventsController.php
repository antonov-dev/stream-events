<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetEventsRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class EventsController extends Controller
{
    use ApiResponse;

    /**
     * Displays list events in asc order
     * @param GetEventsRequest $request
     * @return JsonResponse
     */
    public function index(GetEventsRequest $request)
    {
        $request->validated($request->all());

        $key = 'events:' . $request->user()->id . ':' . $request->last . ':' . $request->limit;
        $ttl = 5 * 60;

        $result = Cache::remember($key, $ttl, function () use ($request) {

            // Retrieve events from events table
            $events = Event::where('user_id', $request->user()->id)
                ->whereRaw('created_at > FROM_UNIXTIME("'.$request->last.'")')
                ->orderBy('created_at', 'asc')
                ->take($request->limit)
                ->get();

            $groupedEvents = $events->groupBy('eventable_type');

            // Retrieve related event instances by ids
            foreach ($groupedEvents as $model => $instances) {
                $groupedEvents[$model] = $this->populate($model, $instances->pluck('eventable_id')->all());
            }

            // Add related event instance and prepare for json output
            return $events->map(function ($item) use ($groupedEvents) {
                $item->eventable = $groupedEvents[$item->eventable_type][$item->eventable_id];
                return new EventResource($item);
            })->all();
        });

        // Remove cache for first run when seeding is not finished yet
        if(!$result) {
            Cache::forget($key);
        }

        return $this->success($result);
    }

    /**
     * Populate data for provided ids
     * @param string $model
     * @param array $ids
     * @return array
     */
    protected function populate(string $model, array $ids): array
    {
        $result = [];
        $resource = explode('\\', $model);
        $resource = 'App\Http\Resources\\' . array_pop($resource) . 'Resource';

        $model::whereIn('id', $ids)->get()->each(function ($item) use (&$result, $resource) {
            $result[$item->id] = new $resource($item);
        });

        return $result;
    }
}
