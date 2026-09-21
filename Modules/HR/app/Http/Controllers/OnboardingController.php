<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Enums\OnboardingStatus;
use Modules\HR\Enums\OnboardingTaskCategory;
use Modules\HR\Http\Requests\ConvertToEmployeeRequest;
use Modules\HR\Http\Requests\StoreOnboardingRequest;
use Modules\HR\Models\Candidate;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobOffer;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\Onboarding;
use Modules\HR\Repositories\OnboardingRepository;
use Modules\HR\Services\OnboardingService;
use Yajra\DataTables\Facades\DataTables;

class OnboardingController extends Controller
{
    public function __construct(
        protected OnboardingRepository $repository,
        protected OnboardingService $service
    ) {}

    public function index(Request $request)
    {
        $selectedDepartment = $request->get('department_id');
        $selectedStatus     = $request->get('status');

        $kpis = $this->repository->getKpis();
        $departments = Department::with('translations')->where('status', 'active')->get();
        $statuses = OnboardingStatus::cases();

        return view('hr::onboardings.index', compact('kpis', 'departments', 'statuses', 'selectedDepartment', 'selectedStatus'));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'department_id']);
        $query = $this->repository->getDataTableQuery($filters);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('onboarding_code_info', function (Onboarding $item) {
                return '<div>
                            <a href="' . route('hr.onboardings.show', $item->id) . '" class="fw-bold text-primary font-14">
                                <i class="bx bx-compass me-1"></i>' . e($item->onboarding_code) . '
                            </a>
                            <small class="text-muted d-block">' . __('Start') . ': ' . $item->start_date->format('Y-m-d') . '</small>
                        </div>';
            })
            ->addColumn('candidate_info', function (Onboarding $item) {
                $cand = $item->candidate;
                if (!$cand) return '<span class="text-muted">-</span>';

                $avatar = '<div class="avatar avatar-sm me-2 d-inline-block">
                                <span class="avatar-initial rounded-circle bg-label-info font-11 fw-bold">' . e($cand->initials) . '</span>
                           </div>';

                return '<div class="d-flex align-items-center">
                            ' . $avatar . '
                            <div>
                                <span class="fw-semibold text-heading font-13 d-block">' . e($cand->full_name) . '</span>
                                <small class="text-muted"><i class="bx bx-envelope font-11 me-1"></i>' . e($cand->email) . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('position_info', function (Onboarding $item) {
                $locale = app()->getLocale();
                $title = $item->jobTitle?->translate($locale)?->title ?? $item->jobTitle?->title ?? '-';
                $dept  = $item->department?->translate($locale)?->name ?? $item->department?->name ?? '-';

                return '<div>
                            <span class="fw-semibold text-heading font-13 d-block">' . e($title) . '</span>
                            <small class="text-muted"><i class="bx bx-building font-11 me-1"></i>' . e($dept) . '</small>
                        </div>';
            })
            ->addColumn('progress_bar', function (Onboarding $item) {
                $pct = $item->progress_percentage;
                $color = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning');

                return '<div class="w-100" style="min-width: 120px;">
                            <div class="d-flex justify-content-between font-11 mb-1">
                                <span class="fw-semibold text-heading">' . $pct . '%</span>
                                <span class="text-muted">' . $item->tasks->where('is_completed', true)->count() . '/' . $item->tasks->count() . ' ' . __('tasks') . '</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar ' . $color . '" role="progressbar" style="width: ' . $pct . '%" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>';
            })
            ->addColumn('conversion_status', function (Onboarding $item) {
                if ($item->employee_id && $item->employee) {
                    $empName = $item->employee->translate(app()->getLocale())?->name ?? $item->employee->name;
                    return '<span class="badge bg-label-success rounded-pill font-11">
                                <i class="bx bx-check-double me-1 font-11"></i>' . __('Converted') . ' (' . e($item->employee->employee_code) . ')
                            </span>';
                }

                return '<span class="badge bg-label-warning rounded-pill font-11">
                            <i class="bx bx-user-plus me-1 font-11"></i>' . __('Pending Conversion') . '
                        </span>';
            })
            ->addColumn('status_badge', function (Onboarding $item) {
                return '<span class="badge ' . $item->status->badgeClass() . ' rounded-pill px-2.5 py-1 font-11">
                            <i class="' . $item->status->icon() . ' me-1 font-11"></i>' . $item->status->label() . '
                        </span>';
            })
            ->addColumn('actions', function (Onboarding $item) {
                $showBtn = '<a href="' . route('hr.onboardings.show', $item->id) . '" class="btn btn-sm btn-icon btn-outline-secondary" title="' . __('View Journey') . '">
                                <i class="bx bx-show font-16"></i>
                            </a>';

                $convertBtn = '';
                if (!$item->employee_id) {
                    $convertBtn = '<button type="button" class="btn btn-sm btn-icon btn-outline-success btn-convert-modal" data-id="' . $item->id . '" title="' . __('Convert to Employee') . '">
                                    <i class="bx bx-user-check font-16"></i>
                                   </button>';
                }

                return '<div class="d-flex align-items-center justify-content-center gap-1">
                            ' . $showBtn . $convertBtn . '
                        </div>';
            })
            ->rawColumns(['onboarding_code_info', 'candidate_info', 'position_info', 'progress_bar', 'conversion_status', 'status_badge', 'actions'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $candidateId = $request->get('candidate_id');
        $candidates  = Candidate::latest()->get();
        $departments = Department::with('translations')->where('status', 'active')->get();
        $jobTitles   = JobTitle::with('translations')->where('status', 'active')->get();
        $employees   = Employee::with('translations')->where('status', 'active')->get();

        return view('hr::onboardings.create', compact('candidates', 'candidateId', 'departments', 'jobTitles', 'employees'));
    }

    public function store(StoreOnboardingRequest $request)
    {
        $onboarding = $this->service->startOnboarding($request->validated(), auth()->id() ?? 1);

        return redirect()->route('hr.onboardings.show', $onboarding->id)
            ->with('success', __('Onboarding journey initiated successfully.'));
    }

    public function show(int $id)
    {
        $onboarding = $this->repository->findWithRelations($id);
        if (!$onboarding) {
            abort(404, __('Onboarding journey not found.'));
        }

        $departments     = Department::with('translations')->where('status', 'active')->get();
        $jobTitles       = JobTitle::with('translations')->where('status', 'active')->get();
        $employees       = Employee::with('translations')->where('status', 'active')->get();
        $employmentTypes = EmploymentType::cases();
        $genders         = Gender::cases();
        $taskCategories  = OnboardingTaskCategory::cases();

        return view('hr::onboardings.show', compact(
            'onboarding',
            'departments',
            'jobTitles',
            'employees',
            'employmentTypes',
            'genders',
            'taskCategories'
        ));
    }

    public function toggleTask(int $taskId): JsonResponse
    {
        try {
            $result = $this->service->toggleTask($taskId);
            return response()->json([
                'success' => true,
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function addTask(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'notes'       => 'nullable|string',
        ]);

        try {
            $task = $this->service->addTask($id, $request->only(['title', 'category', 'assigned_to', 'notes']));
            return response()->json([
                'success' => true,
                'message' => __('Task added to onboarding checklist successfully.'),
                'task'    => $task,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function convertToEmployee(ConvertToEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $employee = $this->service->convertToEmployee($id, $request->validated());

            return response()->json([
                'success'     => true,
                'message'     => __('Candidate successfully converted to an official employee!'),
                'employee_id' => $employee->id,
                'redirect'    => route('hr.employees.show', $employee->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(int $id)
    {
        $onboarding = Onboarding::findOrFail($id);
        $onboarding->delete();

        return redirect()->route('hr.onboardings.index')
            ->with('success', __('Onboarding journey removed.'));
    }
}
