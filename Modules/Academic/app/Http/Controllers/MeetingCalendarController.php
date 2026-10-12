<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreMeetingCalendarRequest;
use Modules\Academic\Http\Requests\UpdateMeetingCalendarRequest;
use Modules\Academic\Models\MeetingCalendar;
use Modules\Academic\Services\MeetingCalendarService;
use Modules\Nursery\Models\Nursery;

class MeetingCalendarController extends Controller
{
    //------------------------------
    public function __construct(
        protected MeetingCalendarService $service
    ) {}

    //------------------------------
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $filters = $request->only(['status', 'institution_id', 'search']);
            $items = $this->service->all(auth()->id(), $filters);

            return response()->json([
                'success' => true,
                'data'    => $items,
            ]);
        }

        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $statuses     = MeetingCalendar::statuses();
        $statusColors = MeetingCalendar::statusColors();
        $sidebarData  = $this->service->getSidebarData(auth()->id());

        return view('academic::meeting-calendar.index', compact(
            'institutions',
            'statuses',
            'statusColors',
            'sidebarData'
        ));
    }

    //------------------------------
    public function sidebarData(): JsonResponse
    {
        $data = $this->service->getSidebarData(auth()->id());

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
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

        $events = $this->service->getCalendarEvents(auth()->id(), $filters);

        return response()->json($events);
    }

    //------------------------------
    public function store(StoreMeetingCalendarRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $meeting = $this->service->create($data);

            return response()->json([
                'success' => true,
                'message' => __('Meeting created successfully.'),
                'data'    => $meeting,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to create meeting.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id, auth()->id());

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => __('Meeting not found.'),
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
                'status'           => $item->status,
                'color'            => $item->color,
            ],
        ]);
    }

    //------------------------------
    public function update(UpdateMeetingCalendarRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->service->update($id, $request->validated(), auth()->id());

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Meeting not found or cannot be updated.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Meeting updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update meeting.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id, auth()->id());

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Meeting not found or cannot be deleted.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Meeting deleted successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete meeting.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:planned,in_progress,completed,cancelled'],
        ]);

        try {
            $updated = $this->service->updateStatus($id, $request->string('status'), auth()->id());

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Meeting not found or cannot be updated.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Meeting status updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update status.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }
}
