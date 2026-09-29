<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Http\Requests\StoreEmployeeRequest;
use Modules\HR\Http\Requests\UpdateEmployeeRequest;
use Modules\HR\Models\Employee;
use Modules\HR\Services\EmployeeService;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    //======================
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    //======================
    /**
     * Display employees listing page.
     */
    public function index(Request $request): View
    {
        $formData = $this->employeeService->getFormData();

        return view('hr::employees.index', $formData);
    }

    //======================
    /**
     * Server-side DataTables endpoint for Employees.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->employeeService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->skipAutoFilter()
            ->addIndexColumn()
            ->addColumn('employee_info', function (Employee $item) {
                $avatar = $item->avatar_url;
                $nameAr = $item->translate('ar')?->name ?? '';
                $nameEn = $item->translate('en')?->name ?? '';
                $displayName = app()->getLocale() === 'ar' ? ($nameAr ?: $nameEn) : ($nameEn ?: $nameAr);
                $code = e($item->employee_code);



                $completion = $item->profile_completion;
                $percent = (int) $completion['percentage'];
                $color = $completion['color'];
                $badgeClass = $completion['class'];
                $showUrl = route('hr.employees.show', $item->id);

                // SVG Circular Progress Ring (r = 21, Circumference = 2 * PI * 21 ≈ 131.95)
                $circumference = 131.95;
                $dashOffset = round($circumference - ($circumference * ($percent / 100)), 2);

                return '
                    <div class="d-flex align-items-center">
                        <a href="' . $showUrl . '" class="position-relative d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0 text-decoration-none" style="width: 48px; height: 48px;" data-bs-toggle="tooltip" data-bs-placement="top" title="' . __('Profile Completion') . ': ' . $percent . '% (' . $completion['label'] . ')">
                            <svg class="position-absolute top-0 start-0" width="48" height="48" viewBox="0 0 48 48" style="transform: rotate(-90deg);">
                                <circle cx="24" cy="24" r="21" stroke="#e9ecef" stroke-width="3" fill="none" />
                                <circle cx="24" cy="24" r="21" stroke="' . $color . '" stroke-width="3" stroke-dasharray="' . $circumference . '" stroke-dashoffset="' . $dashOffset . '" stroke-linecap="round" fill="none" style="transition: stroke-dashoffset 0.6s ease;" />
                            </svg>
                            <img src="' . e($avatar) . '" alt="' . e($displayName) . '" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover; z-index: 1;">
                            <span class="position-absolute badge ' . $badgeClass . ' rounded-pill text-white fw-bold" style="bottom: -5px; font-size: 8.5px; padding: 1px 4.5px; line-height: 1.1; z-index: 2; box-shadow: 0 1px 3px rgba(0,0,0,0.18);">' . $percent . '%</span>
                        </a>
                        <div class="d-flex flex-column">
                            <a href="' . $showUrl . '" class="text-heading fw-bold fs-6 text-truncate mb-1 text-decoration-none hover-primary">' . e($displayName) . '</a>
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="badge bg-label-primary py-0 px-2 font-11">' . $code . '</span>
                                ' . ($item->is_teacher ? '<span class="badge bg-label-info py-0 px-2 font-11 d-inline-flex align-items-center gap-1"><i class="bx bx-book-reader font-12"></i> ' . __('Teacher') . '</span>' : '') . '
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('job_info', function (Employee $item) {
                $locale = app()->getLocale();
                $jobTitle = $item->jobTitle?->translate($locale)?->name ?? $item->jobTitle?->translate('ar')?->name ?? $item->jobTitle?->translate('en')?->name ?? '-';
                $department = $item->department?->translate($locale)?->name ?? $item->department?->translate('ar')?->name ?? $item->department?->translate('en')?->name ?? '-';
                $branch = $item->primaryBranch?->translate($locale)?->name ?? $item->primaryBranch?->translate('ar')?->name ?? $item->primaryBranch?->translate('en')?->name ?? '-';

                return '
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark font-14">' . e($jobTitle) . '</span>
                        <small class="d-flex align-items-center gap-1 mt-1 text-secondary">
                            <i class="bx bx-sitemap text-primary font-14"></i>
                            <span>' . e($department) . '</span>
                        </small>
                        <small class="d-flex align-items-center gap-1 mt-1">
                            <i class="bx bx-store-alt text-warning font-14"></i>
                            <span class="badge bg-label-warning text-warning-emphasis font-11 py-0 px-2">' . e($branch) . '</span>
                        </small>
                    </div>
                ';
            })
            ->addColumn('work_info', function (Employee $item) {
                $locale = app()->getLocale();
                $schedule = $item->workSchedule?->translate($locale)?->name ?? $item->workSchedule?->translate('ar')?->name ?? $item->workSchedule?->translate('en')?->name ?? '-';
                $shift = $item->shift?->translate($locale)?->name ?? $item->shift?->translate('ar')?->name ?? $item->shift?->translate('en')?->name ?? '-';
                $empType = $item->employment_type ? $item->employment_type->label() : '-';

                $empTypeClass = match($item->employment_type?->value) {
                    'full_time' => 'bg-label-primary',
                    'part_time' => 'bg-label-warning',
                    'contract'  => 'bg-label-info',
                    default     => 'bg-label-secondary',
                };
                $empTypeIcon = match($item->employment_type?->value) {
                    'full_time' => 'bx-briefcase',
                    'part_time' => 'bx-time-five',
                    'contract'  => 'bx-file',
                    default     => 'bx-check-circle',
                };

                return '
                    <div class="d-flex flex-column">
                        <div>
                            <span class="badge ' . $empTypeClass . ' px-2 py-1 font-11 d-inline-flex align-items-center mb-1">
                                <i class="bx ' . $empTypeIcon . ' me-1 font-12"></i>' . e($empType) . '
                            </span>
                        </div>
                        <small class="d-flex align-items-center gap-1 text-muted">
                            <i class="bx bx-calendar-check text-info font-14"></i>
                            <span class="text-truncate">' . e($schedule) . '</span>
                        </small>
                        <small class="d-flex align-items-center gap-1 mt-1 text-muted">
                            <i class="bx bx-sun text-warning font-14"></i>
                            <span class="text-truncate">' . e($shift) . '</span>
                        </small>
                    </div>
                ';
            })
            ->addColumn('contact_info', function (Employee $item) {
                $phone = e($item->phone ?? '-');
                $email = $item->email 
                    ? '<a href="mailto:' . e($item->email) . '" class="text-muted small d-inline-flex align-items-center gap-1 mt-1 text-decoration-none" title="' . e($item->email) . '">
                           <i class="bx bx-envelope text-info font-14 flex-shrink-0"></i>
                           <span class="" dir="ltr">' . e($item->email) . '</span>
                       </a>' 
                    : '';

                return '
                    <div class="d-flex flex-column align-items-start">
                        <a href="tel:' . $phone . '" class="fw-bold text-heading d-inline-flex align-items-center gap-1 text-decoration-none">
                            <i class="bx bx-phone-call text-success font-15 flex-shrink-0"></i>
                            <span dir="ltr">' . $phone . '</span>
                        </a>
                        ' . $email . '
                    </div>
                ';
            })
            ->addColumn('salary_info', function (Employee $item) {
                return ''; // Fallback for cached clients
            })
            ->editColumn('status', function (Employee $item) {
                $statusEnum = $item->status;
                $color = match($statusEnum?->value) {
                    'active'     => ['bg' => 'bg-label-success', 'dot' => 'bg-success', 'border' => 'border-success-subtle'],
                    'on_leave'   => ['bg' => 'bg-label-info',    'dot' => 'bg-info',    'border' => 'border-info-subtle'],
                    'probation'  => ['bg' => 'bg-label-warning', 'dot' => 'bg-warning', 'border' => 'border-warning-subtle'],
                    'suspended'  => ['bg' => 'bg-label-danger',  'dot' => 'bg-danger',  'border' => 'border-danger-subtle'],
                    'resigned'   => ['bg' => 'bg-label-secondary','dot' => 'bg-secondary','border' => 'border-secondary-subtle'],
                    'terminated' => ['bg' => 'bg-label-dark',    'dot' => 'bg-dark',    'border' => 'border-dark-subtle'],
                    default      => ['bg' => 'bg-label-secondary','dot' => 'bg-secondary','border' => 'border-secondary-subtle'],
                };
                $label = $statusEnum ? $statusEnum->label() : ($item->status ?? '-');

                return '
                    <span class="badge rounded-pill ' . $color['bg'] . ' border ' . $color['border'] . ' px-3 py-1 shadow-xs fw-semibold d-inline-flex align-items-center gap-1">
                        <span class="rounded-circle ' . $color['dot'] . '" style="width: 7px; height: 7px; display: inline-block;"></span>
                        <span>' . e($label) . '</span>
                    </span>
                ';
            })
            ->editColumn('hire_date', function (Employee $item) {
                if (!$item->hire_date) return '-';
                $dateFormatted = $item->hire_date->format('Y-m-d');
                $diff = $item->hire_date->locale(app()->getLocale())->diffForHumans();
                return '
                    <div class="d-flex flex-column align-items-start">
                        <span class="fw-semibold text-heading d-inline-flex align-items-center gap-1">
                            <i class="bx bx-calendar-event text-primary font-14 flex-shrink-0"></i>
                            <span dir="ltr">' . $dateFormatted . '</span>
                        </span>
                        <small class="text-muted font-11 mt-1">' . $diff . '</small>
                    </div>
                ';
            })
            ->addColumn('actions', function (Employee $item) {
                $name = e($item->translate('ar')?->name ?? $item->translate('en')?->name ?? '');
                return '
                    <div class="d-inline-flex gap-1">
                        <a href="' . route('hr.employees.show', $item->id) . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('View Profile') . '">
                            <i class="icon-base bx bx-user"></i>
                        </a>
                        <a href="' . route('hr.employees.edit', $item->id) . '" class="btn btn-icon btn-sm btn-outline-primary" title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </a>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-employee" 
                            data-id="' . $item->id . '" 
                            data-name="' . $name . '" 
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['employee_info', 'job_info', 'work_info', 'contact_info', 'salary_info', 'status', 'hire_date', 'actions'])
            ->filterColumn('employee_info', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereTranslationLike('name', "%{$keyword}%")
                      ->orWhere('employee_code', 'LIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereTranslationLike('name', "%{$keyword}%")
                      ->orWhere('employee_code', 'LIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $query->where('phone', 'LIKE', "%{$keyword}%");
            })
            ->make(true);
    }

    //======================
    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $formData = $this->employeeService->getFormData();

        return view('hr::employees.create', $formData);
    }

    //======================
    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated(), auth()->id());

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => __('Employee created successfully.'),
                    'employee' => $employee,
                ]);
            }

            return redirect()->route('hr.employees.index')
                ->with('success', __('Employee created successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while creating the employee: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Display the specified employee profile.
     */
    public function show(int $id): View|RedirectResponse
    {
        $profileData = $this->employeeService->getProfileData($id);

        if (!$profileData) {
            return redirect()->route('hr.employees.index')
                ->with('error', __('Employee not found.'));
        }

        return view('hr::employees.show', $profileData);
    }

    //======================
    /**
     * Show the form for editing the specified employee.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $employee = $this->employeeService->getEmployeeById($id);

        if (!$employee) {
            return redirect()->route('hr.employees.index')
                ->with('error', __('Employee not found.'));
        }

        $formData = $this->employeeService->getFormData();
        $formData['employee'] = $employee;

        return view('hr::employees.edit', $formData);
    }

    //======================
    /**
     * Update the specified employee in storage.
     */
    public function update(UpdateEmployeeRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $employee = $this->employeeService->getEmployeeById($id);
            if (!$employee) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('Employee not found.')], 404);
                }
                return redirect()->route('hr.employees.index')->with('error', __('Employee not found.'));
            }

            $this->employeeService->updateEmployee($employee, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Employee updated successfully.'),
                ]);
            }

            return redirect()->route('hr.employees.index')
                ->with('success', __('Employee updated successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while updating the employee: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        try {
            $employee = $this->employeeService->getEmployeeById($id);
            if (!$employee) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('Employee not found.')], 404);
                }
                return redirect()->route('hr.employees.index')->with('error', __('Employee not found.'));
            }

            $this->employeeService->deleteEmployee($employee);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Employee deleted successfully.'),
                ]);
            }

            return redirect()->route('hr.employees.index')
                ->with('success', __('Employee deleted successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('hr.employees.index')
                ->with('error', __('An error occurred while deleting the employee: ') . $e->getMessage());
        }
    }
}
