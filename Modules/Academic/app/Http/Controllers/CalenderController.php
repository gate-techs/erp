<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreCalenderRequest;
use Modules\Academic\Http\Requests\UpdateCalenderRequest;
use Modules\Academic\Models\Calender;
use Modules\Academic\Services\CalenderService;
use Modules\Nursery\Models\Nursery;

class CalenderController extends Controller
{
    //------------------------------
    public function __construct(
        protected CalenderService $service
    ) {}

    //------------------------------
    public function index()
    {
        abort_if(! auth()->user()->can('academic.calender.view'), 403);

        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $statuses = Calender::statuses();

        return view('academic::calender.index', compact(
            'institutions',
            'statuses'
        ));
    }

    //------------------------------
    public function calendarEvents(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.calender.view'), 403);

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
    public function store(StoreCalenderRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.calender.create'), 403);

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
        abort_if(! auth()->user()->can('academic.calender.view'), 403);

        $calender = $this->service->find($id);

        if (! $calender) {
            return response()->json([
                'success' => false,
                'message' => __('Event not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $calender->id,
                'institution_id'   => $calender->institution_id,
                'start_date'       => $calender->start_date ? $calender->start_date->format('Y-m-d') : null,
                'end_date'         => $calender->end_date ? $calender->end_date->format('Y-m-d') : null,
                'status'           => $calender->status,
                'title_ar'         => $calender->translate('ar')?->title ?? '',
                'title_en'         => $calender->translate('en')?->title ?? '',
                'description_ar'   => $calender->translate('ar')?->description ?? '',
                'description_en'   => $calender->translate('en')?->description ?? '',
                'institution_name' => $calender->institution?->translate(app()->getLocale())?->name ?? $calender->institution?->name,
            ],
        ]);
    }

    //------------------------------
    public function update(UpdateCalenderRequest $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.calender.edit'), 403);

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
        abort_if(! auth()->user()->can('academic.calender.delete'), 403);

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
        abort_if(! auth()->user()->can('academic.calender.toggle-status'), 403);

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
