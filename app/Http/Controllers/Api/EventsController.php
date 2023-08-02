<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetEventsRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Repositories\EventRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class EventsController extends Controller
{
    use ApiResponse;

    /**
     * @var EventRepository
     */
    protected EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Displays list events in asc order
     * @param GetEventsRequest $request
     * @return JsonResponse
     */
    public function index(GetEventsRequest $request)
    {
        $request->validated($request->all());

        $this->eventRepository->setUser($request->user())
            ->setTTL(5 * 60);

        return $this->success($this->eventRepository->getList(
            $request->input('last', 0),
            $request->input('limit', 100)
        ));
    }

    /**
     * Update event
     * @param UpdateEventRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateEventRequest $request, int $id)
    {
        $request->validated($request->all());

        $result = $this->eventRepository
            ->setUser($request->user())
            ->update($id, $request->only(['is_read']));

        return $result
            ? $this->success([], 'Event successfully updated')
            : $this->error([], 'Event not found', 404);
    }
}
