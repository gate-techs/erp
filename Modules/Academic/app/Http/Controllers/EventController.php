<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreEventRequest;
use Modules\Academic\Http\Requests\UpdateEventRequest;
use Modules\Academic\Models\Event;
use Modules\Academic\Services\EventService;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService
    ) {}

    /**
     * Display a listing of events.
     */
    public function index(Request $request): View
    {
        $formData = $this->eventService->getFormData();

        return view('academic::events.index', $formData);
    }

    /**
     * Get Server-Side DataTables JSON.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->eventService->getFilteredQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image_url', fn (Event $event) => $event->image_url)
            ->addColumn('name_ar', fn (Event $event) => $event->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn (Event $event) => $event->translate('en')?->name ?? '-')
            ->addColumn('name_display', fn (Event $event) => $event->translate(app()->getLocale())?->name ?? $event->translate('ar')?->name ?? '-')
            ->addColumn('description_display', fn (Event $event) => $event->translate(app()->getLocale())?->description ?? $event->translate('ar')?->description ?? '')
            ->addColumn('institution_name', fn (Event $event) => $event->institution?->translate(app()->getLocale())?->name ?? $event->institution?->translate('ar')?->name ?? '-')
            ->addColumn('branch_name', fn (Event $event) => $event->branch?->translate(app()->getLocale())?->name ?? $event->branch?->translate('ar')?->name ?? '-')
            ->addColumn('class_name', fn (Event $event) => $event->classroom ? ($event->classroom->translate(app()->getLocale())?->name ?? $event->classroom->translate('ar')?->name ?? '-') : __('All Classes'))
            ->addColumn('event_type_name', fn (Event $event) => $event->eventType ? ($event->eventType->translate(app()->getLocale())?->name ?? $event->eventType->translate('ar')?->name ?? '-') : '-')
            ->editColumn('price', fn (Event $event) => number_format((float) $event->price, 3))
            ->addColumn('dates_formatted', function (Event $event) {
                $start = $event->start_date ? $event->start_date->format('Y-m-d') : '-';
                $end = $event->end_date ? $event->end_date->format('Y-m-d') : $start;
                return [
                    'start' => $start,
                    'end'   => $end,
                    'full'  => ($start === $end) ? $start : "{$start} " . __('to') . " {$end}",
                ];
            })
            ->addColumn('actions', function (Event $event) {
                return [
                    'show_url'   => route('academic.events.show', $event->id),
                    'edit_url'   => route('academic.events.edit', $event->id),
                    'delete_url' => route('academic.events.destroy', $event->id),
                ];
            })
            ->filterColumn('name_display', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%");
            })
            ->filter(function ($query) {
                if ($search = request('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->whereTranslationLike('name', "%{$search}%")
                          ->orWhereTranslationLike('description', "%{$search}%")
                          ->orWhere('price', 'like', "%{$search}%")
                          ->orWhere('id', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        $formData = $this->eventService->getFormData();

        return view('academic::events.create', $formData);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(StoreEventRequest $request): JsonResponse|RedirectResponse
    {
        $event = $this->eventService->create(
            $request->validated(),
            auth()->id(),
            $request->file('image')
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Event created successfully.'),
                'data'    => $event,
            ]);
        }

        return redirect()->route('academic.events.index')
            ->with('success', __('Event created successfully.'));
    }

    /**
     * Display the specified event.
     */
    public function show(int $id): View
    {
        $event = $this->eventService->find($id);
        abort_unless($event, 404, __('Event not found.'));

        return view('academic::events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Request $request, int $id): View|JsonResponse
    {
        $event = $this->eventService->find($id);
        abort_unless($event, 404, __('Event not found.'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'                => $event->id,
                'name_ar'           => $event->translate('ar')?->name ?? '',
                'name_en'           => $event->translate('en')?->name ?? '',
                'description_ar'    => $event->translate('ar')?->description ?? '',
                'description_en'    => $event->translate('en')?->description ?? '',
                'institution_id'    => $event->institution_id,
                'branch_id'         => $event->branch_id,
                'academic_class_id' => $event->academic_class_id,
                'event_type_id'     => $event->event_type_id,
                'start_date'        => $event->start_date ? $event->start_date->format('Y-m-d') : '',
                'end_date'          => $event->end_date ? $event->end_date->format('Y-m-d') : '',
                'start_time'        => $event->start_time ? substr($event->start_time, 0, 5) : '',
                'end_time'          => $event->end_time ? substr($event->end_time, 0, 5) : '',
                'price'             => (float) $event->price,
                'status'            => $event->status,
                'image_url'         => $event->image_url,
            ]);
        }

        $formData = $this->eventService->getFormData($event->institution_id, $event->branch_id);

        return view('academic::events.edit', array_merge($formData, compact('event')));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(UpdateEventRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $updated = $this->eventService->update(
            $id,
            $request->validated(),
            $request->file('image')
        );

        abort_unless($updated, 404, __('Event not found.'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Event updated successfully.'),
            ]);
        }

        return redirect()->route('academic.events.index')
            ->with('success', __('Event updated successfully.'));
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $deleted = $this->eventService->delete($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? __('Event deleted successfully.') : __('Failed to delete event.'),
            ]);
        }

        return redirect()->route('academic.events.index')
            ->with('success', __('Event deleted successfully.'));
    }

    /**
     * Toggle event status (active / inactive).
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $toggled = $this->eventService->toggleStatus($id);
        $event = $this->eventService->find($id);

        return response()->json([
            'success' => $toggled,
            'status'  => $event?->status,
            'message' => __('Event status updated successfully.'),
        ]);
    }

    /**
     * AJAX: Get branches for an institution.
     */
    public function branches(int $institutionId): JsonResponse
    {
        $branches = $this->eventService->getBranches($institutionId);

        return response()->json($branches->map(fn ($b) => [
            'id'   => $b->id,
            'name' => $b->translate(app()->getLocale())?->name ?? $b->translate('ar')?->name ?? $b->name,
        ]));
    }

    /**
     * AJAX: Get classes for a branch.
     */
    public function classes(int $branchId): JsonResponse
    {
        $classes = $this->eventService->getClasses($branchId);

        return response()->json($classes->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $c->translate(app()->getLocale())?->name ?? $c->translate('ar')?->name ?? $c->name,
        ]));
    }
}
