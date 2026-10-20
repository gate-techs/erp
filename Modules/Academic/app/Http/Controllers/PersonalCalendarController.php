<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StorePersonalCalendarRequest;
use Modules\Academic\Http\Requests\UpdatePersonalCalendarRequest;
use Modules\Academic\Models\PersonalCalendar;
use Modules\Academic\Services\PersonalCalendarService;
use Modules\Nursery\Models\Nursery;

class PersonalCalendarController extends Controller
{
    //------------------------------
    public function __construct(
        protected PersonalCalendarService $service
    ) {}

    //------------------------------
    public function index(Request $request)
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.view'), 403);

        if ($request->wantsJson()) {
            $filters = $request->only(['status', 'institution_id', 'search']);
            $items = $this->service->all(auth()->id(), $filters);

            return response()->json([
                'success' => true,
                'data'    => $items,
            ]);
        }

        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $statuses     = PersonalCalendar::statuses();
        $statusColors = PersonalCalendar::statusColors();

        return view('academic::personal-calendar.index', compact(
            'institutions',
            'statuses',
            'statusColors'
        ));
    }

    //------------------------------
    public function calendarEvents(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.view'), 403);

        $filters = $request->only([
            'institution_id',
            'status',
            'start',
            'end',
        ]);

        $events = $this->service->getCalendarEvents(auth()->id(), $filters);

        return response()->json($events);
    }

    //------------------------------
    public function store(StorePersonalCalendarRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.create'), 403);

        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $event = $this->service->create($data);

            return response()->json([
                'success' => true,
                'message' => __('Personal event created successfully.'),
                'data'    => $event,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to create personal event.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.view'), 403);

        $item = $this->service->find($id, auth()->id());

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => __('Event not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $item->id,
                'institution_id'   => $item->institution_id,
                'institution_name' => $item->institution?->translate(app()->getLocale())?->name ?? $item->institution?->name,
                'title'            => $item->title,
                'description'      => $item->description ?? '',
                'event_date'       => $item->event_date ? $item->event_date->format('Y-m-d') : null,
                'start_time'       => $item->start_time ? substr($item->start_time, 0, 5) : '',
                'end_time'         => $item->end_time ? substr($item->end_time, 0, 5) : '',
                'is_all_day'       => (bool) $item->is_all_day,
                'status'           => $item->status,
                'color'            => $item->color,
            ],
        ]);
    }

    //------------------------------
    public function update(UpdatePersonalCalendarRequest $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.edit'), 403);

        try {
            $updated = $this->service->update($id, $request->validated(), auth()->id());

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Event not found or cannot be updated.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Personal event updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update personal event.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.delete'), 403);

        try {
            $deleted = $this->service->delete($id, auth()->id());

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Event not found or cannot be deleted.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Personal event deleted successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete personal event.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.personal-calendar.status'), 403);

        $request->validate([
            'status' => ['required', 'in:planned,in_progress,completed,cancelled'],
        ]);

        try {
            $updated = $this->service->updateStatus($id, $request->string('status'), auth()->id());

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Event not found or cannot be updated.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Event status updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update status.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }
}
