<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Http\Requests\StoreWorkScheduleRequest;
use Modules\HR\Http\Requests\UpdateWorkScheduleRequest;
use Modules\HR\Models\WorkSchedule;
use Modules\HR\Models\WorkScheduleDay;
use Modules\HR\Services\WorkScheduleService;
use Yajra\DataTables\Facades\DataTables;

class WorkScheduleController extends Controller
{
    //======================
    public function __construct(
        protected WorkScheduleService $workScheduleService
    ) {}

    //======================
    /**
     * Display listing view for Work Schedules in HR Settings.
     */
    public function index(): View
    {
        abort_if(! auth()->user()->can('hr.settings.work-schedules.view'), 403);

        $formData = $this->workScheduleService->getFormData();

        return view('hr::settings.work_schedules.index', $formData);
    }

    //======================
    /**
     * Server-side DataTables endpoint for Work Schedules.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.work-schedules.view'), 403);

        $query = $this->workScheduleService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(WorkSchedule $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(WorkSchedule $item) => $item->translate('en')?->name ?? '-')
            ->editColumn('code', fn(WorkSchedule $item) => $item->code ?? '-')
            ->addColumn('type_badge', function (WorkSchedule $item) {
                $typeEnum = $item->type;
                $color = $typeEnum ? $typeEnum->badgeColor() : 'bg-label-primary';
                $label = $typeEnum ? $typeEnum->label() : ($item->type ?? '-');
                return '<span class="badge ' . $color . '">' . $label . '</span>';
            })
            ->addColumn('working_days_info', function (WorkSchedule $item) {
                $days = $item->working_days_count ?? 0;
                $hours = $item->total_weekly_hours ? $item->total_weekly_hours . ' ' . __('hrs/wk') : '-';
                return '<div class="d-flex flex-column">
                            <span class="fw-semibold text-heading">' . $days . ' ' . __('days/week') . '</span>
                            <small class="text-muted">' . $hours . '</small>
                        </div>';
            })
            ->addColumn('scope_info', function (WorkSchedule $item) {
                $dept = $item->department?->translate('ar')?->name ?? $item->department?->translate('en')?->name;
                $branch = $item->branch?->translate('ar')?->name ?? $item->branch?->translate('en')?->name;

                if ($dept) {
                    return '<div class="d-flex align-items-center gap-1"><i class="icon-base bx bx-sitemap text-primary font-16"></i><span>' . e($dept) . '</span></div>';
                }
                if ($branch) {
                    return '<div class="d-flex align-items-center gap-1"><i class="icon-base bx bx-store text-info font-16"></i><span>' . e($branch) . '</span></div>';
                }
                return '<span class="badge bg-label-secondary">' . __('All Departments / Central') . '</span>';
            })
            ->addColumn('off_days_badge', function (WorkSchedule $item) {
                $offDays = $item->days->where('is_off_day', true);
                if ($offDays->isEmpty()) {
                    return '<span class="badge bg-label-success">' . __('No Off-Days') . '</span>';
                }

                $names = [];
                $dayNames = WorkScheduleDay::dayNames();
                foreach ($offDays as $day) {
                    $names[] = $dayNames[$day->day_of_week] ?? (string) $day->day_of_week;
                }

                return '<div class="d-flex flex-wrap gap-1">' .
                    implode('', array_map(fn($n) => '<span class="badge bg-label-warning">' . e($n) . '</span>', $names)) .
                    '</div>';
            })
            ->addColumn('color_badge', function (WorkSchedule $item) {
                $color = $item->color ?? '#7367f0';
                return '<div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle shadow-xs" style="display:inline-block; width: 18px; height: 18px; background-color: ' . e($color) . ';"></span>
                            <small class="text-muted">' . e($color) . '</small>
                        </div>';
            })
            ->editColumn('status', function (WorkSchedule $item) {
                $statusEnum = $item->status;
                $badgeClass = $statusEnum ? $statusEnum->badgeColor() : 'bg-label-secondary';
                $label = $statusEnum ? $statusEnum->label() : ($item->status ?? '-');

                $defaultBadge = $item->is_default
                    ? ' <span class="badge bg-primary ms-1" title="' . __('Default Schedule') . '"><i class="icon-base bx bx-star me-1"></i>' . __('Default') . '</span>'
                    : '';

                return '<span class="badge ' . $badgeClass . '">' . $label . '</span>' . $defaultBadge;
            })
            ->editColumn('created_at', fn(WorkSchedule $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (WorkSchedule $item) {
                $user = auth()->user();

                $daysArray = [];
                foreach ($item->days as $d) {
                    $daysArray[$d->day_of_week] = [
                        'day_of_week'     => $d->day_of_week,
                        'is_off_day'      => (bool) $d->is_off_day,
                        'shift_id'        => $d->shift_id,
                        'second_shift_id' => $d->second_shift_id,
                        'work_hours'      => (float) $d->work_hours,
                    ];
                }

                $rowJson = htmlspecialchars(json_encode([
                    'id'                 => $item->id,
                    'code'               => $item->code ?? '',
                    'name_ar'            => $item->translate('ar')?->name ?? '',
                    'name_en'            => $item->translate('en')?->name ?? '',
                    'description_ar'     => $item->translate('ar')?->description ?? '',
                    'description_en'     => $item->translate('en')?->description ?? '',
                    'type'               => is_object($item->type) ? $item->type->value : $item->type,
                    'branch_id'          => $item->branch_id,
                    'department_id'      => $item->department_id,
                    'is_default'         => (bool) $item->is_default,
                    'color'              => $item->color ?? '#7367f0',
                    'status'             => is_object($item->status) ? $item->status->value : $item->status,
                    'working_days_count' => $item->working_days_count,
                    'total_weekly_hours' => $item->total_weekly_hours,
                    'days'               => $daysArray,
                ]), ENT_QUOTES, 'UTF-8');

                $editBtn = '';
                if ($user->can('hr.settings.work-schedules.edit')) {
                    $editBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-schedule" 
                        data-row="' . $rowJson . '" 
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button>';
                }

                $deleteBtn = '';
                if ($user->can('hr.settings.work-schedules.delete')) {
                    $deleteBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-schedule" 
                        data-id="' . $item->id . '" 
                        data-name="' . e($item->translate('ar')?->name ?? $item->translate('en')?->name ?? '') . '" 
                        title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }

                $buttons = $editBtn . $deleteBtn;
                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['type_badge', 'working_days_info', 'scope_info', 'off_days_badge', 'color_badge', 'status', 'actions'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->make(true);
    }

    //======================
    /**
     * Store a newly created work schedule.
     */
    public function store(StoreWorkScheduleRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.work-schedules.create'), 403);

        try {
            $this->workScheduleService->createSchedule($request->validated(), auth()->id());

            return redirect()->route('hr.settings.work-schedules.index')
                ->with('success', __('Work schedule created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while creating the work schedule: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified work schedule.
     */
    public function update(UpdateWorkScheduleRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.work-schedules.edit'), 403);

        try {
            $schedule = $this->workScheduleService->getScheduleById($id);
            if (!$schedule) {
                return redirect()->route('hr.settings.work-schedules.index')
                    ->with('error', __('Work schedule not found.'));
            }

            $this->workScheduleService->updateSchedule($schedule, $request->validated());

            return redirect()->route('hr.settings.work-schedules.index')
                ->with('success', __('Work schedule updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while updating the work schedule: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified work schedule.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.work-schedules.delete'), 403);

        try {
            $schedule = $this->workScheduleService->getScheduleById($id);
            if (!$schedule) {
                return redirect()->route('hr.settings.work-schedules.index')
                    ->with('error', __('Work schedule not found.'));
            }

            $this->workScheduleService->deleteSchedule($schedule);

            return redirect()->route('hr.settings.work-schedules.index')
                ->with('success', __('Work schedule deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('hr.settings.work-schedules.index')
                ->with('error', __('An error occurred while deleting the work schedule: ') . $e->getMessage());
        }
    }
}
