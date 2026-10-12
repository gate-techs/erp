<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Services\AcademicTeacherService;
use Modules\HR\Models\Employee;
use Yajra\DataTables\Facades\DataTables;

class AcademicTeacherController extends Controller
{
    public function __construct(
        protected AcademicTeacherService $service
    ) {}

    //------------------------------------------
    public function index(Request $request)
    {
        $institutions = $this->service->getInstitutions();
        $branches     = $this->service->getBranches();
        $jobTitles    = $this->service->getJobTitles();
        $managers     = $this->service->getManagers();

        return view('academic::teachers.index', compact(
            'institutions',
            'branches',
            'jobTitles',
            'managers'
        ));
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        $filters = $request->only([
            'branch_id',
            'job_title_id',
            'status',
            'search',
        ]);

        $query = $this->service->getFilteredQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('employee_code_formatted', function (Employee $teacher) {
                return '<span class="badge bg-label-dark font-monospace fw-bold">' . e($teacher->employee_code) . '</span>';
            })
            ->addColumn('teacher_name', function (Employee $teacher) {
                $locale = app()->getLocale();
                $name = $teacher->translate($locale)?->name
                    ?? $teacher->translate('ar')?->name
                    ?? $teacher->name
                    ?? '-';
                $avatar = $teacher->avatar_url ?? asset('assets/img/avatars/1.png');

                return '
                    <div class="d-flex align-items-center gap-2">
                        <img src="' . e($avatar) . '" alt="' . e($name) . '" class="rounded-circle shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                        <button type="button" class="btn btn-link p-0 text-start fw-semibold text-heading text-decoration-none teacher-name-text edit-teacher-btn" data-id="' . $teacher->id . '" title="' . e(__('Edit Basic Info')) . '">' . e($name) . '</button>
                    </div>';
            })
            ->addColumn('job_title', function (Employee $teacher) {
                $locale = app()->getLocale();
                $title = $teacher->jobTitle?->translate($locale)?->name
                    ?? $teacher->jobTitle?->translate('ar')?->name
                    ?? $teacher->jobTitle?->name
                    ?? '-';

                return e($title);
            })
            ->addColumn('role', function (Employee $teacher) {
                $roleName = $teacher->user?->roles?->first()?->name ?? __('Teacher');
                return '<span class="badge bg-label-info">' . e(__($roleName)) . '</span>';
            })
            ->addColumn('manager', function (Employee $teacher) {
                $locale = app()->getLocale();
                $managerName = $teacher->manager?->translate($locale)?->name
                    ?? $teacher->manager?->translate('ar')?->name
                    ?? $teacher->manager?->name
                    ?? '-';

                return e($managerName);
            })
            ->addColumn('branch', function (Employee $teacher) {
                $locale = app()->getLocale();
                $branchName = $teacher->branch?->translate($locale)?->name
                    ?? $teacher->branch?->translate('ar')?->name
                    ?? $teacher->branch?->name
                    ?? '-';

                return e($branchName);
            })
            ->addColumn('phone_formatted', function (Employee $teacher) {
                if (! $teacher->phone) {
                    return '<span class="text-muted">-</span>';
                }

                return '<a href="tel:' . e($teacher->phone) . '" class="badge bg-label-secondary font-monospace text-decoration-none" dir="ltr"><i class="icon-base bx bx-phone me-1"></i>' . e($teacher->phone) . '</a>';
            })
            ->addColumn('status_badge', function (Employee $teacher) {
                $isActive = $teacher->status === 'active';
                $label = $isActive ? __('Active') : __('Inactive');
                $class = $isActive ? 'bg-label-success' : 'bg-label-danger';

                return '<span class="badge ' . $class . ' cursor-pointer toggle-status-btn" data-id="' . $teacher->id . '">' . e($label) . '</span>';
            })
            ->addColumn('actions', function (Employee $teacher) {
                $id = $teacher->id;
                $name = e($teacher->translate(app()->getLocale())?->name ?? $teacher->translate('ar')?->name ?? $teacher->name ?? '');

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info assign-teacher-btn"
                            data-id="' . $id . '"
                            title="' . e(__('Assign Stages & Classes')) . '">
                            <i class="icon-base bx bx-layer"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-teacher-btn"
                            data-id="' . $id . '"
                            title="' . e(__('Edit Basic Info')) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-teacher-btn"
                            data-id="' . $id . '"
                            data-name="' . $name . '"
                            title="' . e(__('Delete')) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['employee_code_formatted', 'teacher_name', 'role', 'phone_formatted', 'status_badge', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'employee_code'     => 'nullable|string|max:50|unique:employees,employee_code',
            'primary_branch_id' => 'required|integer|exists:nursery_branches,id',
            'job_title_id'      => 'nullable|integer|exists:job_titles,id',
            'manager_id'        => 'nullable|integer|exists:employees,id',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'status'            => 'required|in:active,inactive',
        ]);

        try {
            $teacher = $this->service->createTeacher($validated);

            return response()->json([
                'success' => true,
                'message' => __('Teacher created successfully.'),
                'data'    => $teacher,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to create teacher: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    public function show(int $id): JsonResponse
    {
        $teacher = $this->service->find($id);

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => __('Teacher not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $teacher->id,
                'employee_code'     => $teacher->employee_code,
                'name'              => $teacher->translate(app()->getLocale())?->name ?? $teacher->name,
                'name_ar'           => $teacher->translate('ar')?->name ?? '',
                'primary_branch_id' => $teacher->primary_branch_id,
                'job_title_id'      => $teacher->job_title_id,
                'manager_id'        => $teacher->manager_id,
                'phone'             => $teacher->phone,
                'email'             => $teacher->email,
                'status'            => $teacher->status,
            ],
        ]);
    }

    //------------------------------------------
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'primary_branch_id' => 'required|integer|exists:nursery_branches,id',
            'job_title_id'      => 'nullable|integer|exists:job_titles,id',
            'manager_id'        => 'nullable|integer|exists:employees,id',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'status'            => 'required|in:active,inactive',
        ]);

        try {
            $updated = $this->service->updateTeacher($id, $validated);

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Teacher not found.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Teacher basic info updated successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update teacher: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->deleteTeacher($id);

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Teacher not found.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Teacher deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete teacher: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        $teacher = $this->service->toggleStatus($id);

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => __('Teacher not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $teacher->status,
            'message' => __('Status updated successfully.'),
        ]);
    }

    //------------------------------------------
    public function getAllocation(int $id): JsonResponse
    {
        $data = $this->service->getAllocationData($id);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => __('Teacher not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    //------------------------------------------
    public function saveAllocation(Request $request, int $id): JsonResponse
    {
        $classes = $request->input('classes', []);
        $subjects = $request->input('subjects', []);

        if (! is_array($classes)) {
            $classes = [];
        }
        if (! is_array($subjects)) {
            $subjects = [];
        }

        try {
            $saved = $this->service->saveAllocation($id, $classes, $subjects);

            if (! $saved) {
                return response()->json([
                    'success' => false,
                    'message' => __('Teacher not found.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Academic assignments saved successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to save academic assignments: ') . $e->getMessage(),
            ], 500);
        }
    }
}
