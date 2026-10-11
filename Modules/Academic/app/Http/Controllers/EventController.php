<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreEventRequest;
use Modules\Academic\Http\Requests\UpdateEventRequest;
use Modules\Academic\Models\Event;
use Modules\Academic\Services\EventService;
use Modules\Nursery\Models\Nursery;

class EventController extends Controller
{
    //------------------------------
    public function __construct(
        protected EventService $service
    ) {}

    //------------------------------
    public function index()
    {
        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $statuses = Event::statuses();

        return view('academic::events.index', compact(
            'institutions',
            'statuses'
        ));
    }

    //------------------------------
    public function calendarEvents(Request $request): JsonResponse
    {
        $filters = $request->only([
            'institution_id',
            'status',
            'start',
            'end',
        ]);

        $events = $this->service->getCalendarEvents($filters);

        return response()->json($events);
    }

    //------------------------------
    public function store(StoreEventRequest $request): JsonResponse
    {
        try {
            $event = $this->service->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => __('Event created successfully.'),
                'data'    => $event,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to create event.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function show(int $id): JsonResponse
    {
        $event = $this->service->find($id);

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => __('Event not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $event->id,
                'institution_id'   => $event->institution_id,
                'start_date'       => $event->start_date ? $event->start_date->format('Y-m-d') : null,
                'end_date'         => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                'status'           => $event->status,
                'title_ar'         => $event->translate('ar')?->title ?? '',
                'title_en'         => $event->translate('en')?->title ?? '',
                'description_ar'   => $event->translate('ar')?->description ?? '',
                'description_en'   => $event->translate('en')?->description ?? '',
                'institution_name' => $event->institution?->translate(app()->getLocale())?->name ?? $event->institution?->name,
            ],
        ]);
    }

    //------------------------------
    public function update(UpdateEventRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->service->update($id, $request->validated());

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Event not found or cannot be updated.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Event updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update event.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Event not found or cannot be deleted.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Event deleted successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete event.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $toggled = $this->service->toggleStatus($id);

            return response()->json([
                'success' => (bool) $toggled,
                'message' => __('Event status updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to toggle status.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }
}
