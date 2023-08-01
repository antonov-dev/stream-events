<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetEventsRequest;
use App\Http\Requests\UpdateEventRequest;
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

        if(Cache::has($key)) {
            $result = Cache::get($key);
        } else {
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
            $result = $events->map(function ($item) use ($groupedEvents) {
                $item->eventable = $groupedEvents[$item->eventable_type][$item->eventable_id];
                return new EventResource($item);
            })->all();

            if($result) {
                Cache::tags('events:' . $request->user()->id)->put($key, $result, $ttl);
            }
        }

        return $this->success($result);
    }

    /**
     * Update event
     * @param UpdateEventRequest $request
     * @param Event $event
     * @return JsonResponse
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $request->validated($request->all());

        $event->update($request->only(['is_read']));

        // Remove cached data for user
        Cache::tags('events:' . $request->user()->id)->flush();

        return $this->success([], 'Event successfully updated');
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
