<?php

namespace App\Repositories;

use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class EventRepository
{
    /**
     * @var User $user
     */
    protected User $user;

    /**
     * @var int $ttl
     */
    protected int $ttl = 5 * 10;

    /**
     * Set user
     * @param User $user
     * @return EventRepository
     */
    public function setUser(User $user): static
    {
      $this->user = $user;

      return $this;
    }

    /**
     * Set ttl
     * @param int $ttl
     * @return EventRepository
     */
    public function setTTL(int $ttl): static
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Returns list events in asc order
     * @param int $last
     * @param int $limit
     * @return array
     */
    public function getList(int $last = 0, int $limit = 100): array
    {
        $key = 'events:' . $this->user->id . ':' . $last . ':' . $limit;

        if(Cache::has($key)) {
            $result = Cache::get($key);
        } else {
            // Retrieve events from events table
            $events = Event::where('user_id', $this->user->id)
                ->whereRaw('created_at > FROM_UNIXTIME("'.$last.'")')
                ->orderBy('created_at', 'asc')
                ->take($limit)
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
                Cache::tags('events:' . $this->user)->put($key, $result, $this->ttl);
            }
        }

        return $result;
    }

    /**
     * Update event instance
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data)
    {
        $result = Event::where('id', $id)
            ->where('user_id', $this->user->id)
            ->update($data);

        // Remove cached data for user
        Cache::tags('events:' . $this->user->id)->flush();

        return (bool) $result;
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
