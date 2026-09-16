<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Http\Requests\StoreDepartmentRequest;
use Modules\HR\Http\Requests\UpdateDepartmentRequest;
use Modules\HR\Models\Department;
use Modules\HR\Services\DepartmentService;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    //======================
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    //======================
    /**
     * Display listing view with lookups for side offcanvas and filters.
     */
    public function index(Request $request): View
    {
        $formData = $this->departmentService->getFormData();

        return view('hr::departments.index', [
            'parentDepartments' => $formData['parentDepartments'],
            'nurseries'         => $formData['nurseries'],
            'branches'          => $formData['branches'],
            'managers'          => $formData['managers'],
            'statuses'          => $formData['statuses'],
        ]);
    }

    //======================
    /**
     * Get Server-Side DataTables JSON response for departments.
     */
    public function data(Request $request): JsonResponse
    {
        $query = Department::with([
            'translations',
            'parent.translations',
            'nursery.translations',
            'branch.translations',
            'manager',
        ])->latest('id');

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Type (main administration vs sub-department)
        if ($request->filled('type')) {
            if ($request->type === 'main') {
                $query->whereNull('parent_id');
            } elseif ($request->type === 'sub') {
                $query->whereNotNull('parent_id');
            }
        }

        // Filter: Parent Department
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Filter: Nursery
        if ($request->filled('nursery_id')) {
            $query->where('nursery_id', $request->nursery_id);
        }

        // Filter: Branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(Department $item) => $item->translateOrNew('ar')->name ?? '-')
            ->addColumn('name_en', fn(Department $item) => $item->translateOrNew('en')->name ?? '-')
            ->addColumn('description_ar', fn(Department $item) => $item->translateOrNew('ar')->description ?? '')
            ->addColumn('description_en', fn(Department $item) => $item->translateOrNew('en')->description ?? '')
            ->addColumn('type_label', function (Department $item) {
                return $item->parent_id
                    ? '<span class="badge bg-label-info">' . __('قسم فرعي') . '</span>'
                    : '<span class="badge bg-label-primary">' . __('إدارة رئيسية') . '</span>';
            })
            ->addColumn('parent_name', fn(Department $item) => $item->parent?->name ?? '<span class="text-muted">' . __('إدارة رئيسية') . '</span>')
            ->addColumn('parent_id', fn(Department $item) => $item->parent_id)
            ->addColumn('nursery_name', fn(Department $item) => $item->nursery?->name ?? '<span class="text-muted">' . __('عام / غير محدد') . '</span>')
            ->addColumn('nursery_id', fn(Department $item) => $item->nursery_id)
            ->addColumn('branch_name', fn(Department $item) => $item->branch?->name ?? '<span class="text-muted">' . __('كل الفروع / مركزي') . '</span>')
            ->addColumn('branch_id', fn(Department $item) => $item->branch_id)
            ->addColumn('manager_name', fn(Department $item) => $item->manager?->name ?? '<span class="text-muted">' . __('غير محدد') . '</span>')
            ->addColumn('manager_id', fn(Department $item) => $item->manager_id)
            ->addColumn('status_label', fn(Department $item) => $item->status->label())
            ->addColumn('status_badge', fn(Department $item) => $item->status->badgeColor())
            ->editColumn('created_at', fn(Department $item) => $item->created_at ? $item->created_at->format('d-m-Y') : '-')
            ->rawColumns(['type_label', 'parent_name', 'nursery_name', 'branch_name', 'manager_name'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->filter(function ($query) {
                if ($search = request('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->whereTranslationLike('name', "%{$search}%")
                          ->orWhereTranslationLike('description', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    //======================
    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse|JsonResponse
    {
        $department = $this->departmentService->createDepartment(
            $request->validated(),
            auth()->id()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Department created successfully.'),
                'data'    => $department,
            ], 201);
        }

        return redirect()
            ->route('hr.departments.index')
            ->with('success', __('Department created successfully.'));
    }

    //======================
    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $department = $this->departmentService->updateDepartment(
            $id,
            $request->validated()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Department updated successfully.'),
                'data'    => $department,
            ]);
        }

        return redirect()
            ->route('hr.departments.index')
            ->with('success', __('Department updated successfully.'));
    }

    //======================
    /**
     * Remove the specified department from storage.
     */
    public function destroy(int $id, Request $request): RedirectResponse|JsonResponse
    {
        $deleted = $this->departmentService->deleteDepartment($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? __('Department deleted successfully.') : __('Failed to delete department.'),
            ], $deleted ? 200 : 422);
        }

        return redirect()
            ->route('hr.departments.index')
            ->with($deleted ? 'success' : 'error', $deleted ? __('Department deleted successfully.') : __('Failed to delete department.'));
    }

    //======================
    /**
     * Fetch branches for a specific nursery via AJAX.
     */
    public function getBranchesByNursery(int $nurseryId): JsonResponse
    {
        $branches = $this->departmentService->getBranchesByNursery($nurseryId);

        return response()->json([
            'success' => true,
            'data'    => $branches->map(fn($b) => [
                'id'   => $b->id,
                'name' => $b->name,
            ]),
        ]);
    }
}
