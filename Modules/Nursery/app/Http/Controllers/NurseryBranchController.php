<?php

namespace Modules\Nursery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Nursery\Http\Requests\StoreNurseryBranchRequest;
use Modules\Nursery\Http\Requests\UpdateNurseryBranchRequest;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Nursery\Services\NurseryBranchService;
use Yajra\DataTables\Facades\DataTables;

class NurseryBranchController extends Controller
{
    //======================
    public function __construct(
        protected NurseryBranchService $branchService
    ) {}

    //======================
    /**
     * Display listing view with lookups for the side drawer.
     */
    public function index(Request $request): View
    {
        abort_if(!auth()->user()->can('nursery.branches.view'), 403);

        $formData = $this->branchService->getFormData();

        return view('nursery::branches.index', [
            'nurseries' => $formData['nurseries'],
            'governments' => $formData['governments'],
            'cities' => $formData['cities'],
            'employees' => $formData['employees'],
        ]);
    }

    //======================
    /**
     * Get Server-Side DataTables JSON response for nursery branches.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.branches.view'), 403);

        $query = NurseryBranch::with([
            'translations',
            'nursery.translations',
            'government.translations',
            'city.translations',
            'employee',
            'workDays',
        ])->latest('id');

        if ($request->filled('nursery_id')) {
            $query->where('nursery_id', $request->nursery_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('government_id')) {
            $query->where('government_id', $request->government_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image_url', fn(NurseryBranch $item) => $item->image_url)
            ->addColumn('name_ar', fn(NurseryBranch $item) => $item->translateOrNew('ar')->name ?? '-')
            ->addColumn('name_en', fn(NurseryBranch $item) => $item->translateOrNew('en')->name ?? '-')
            ->addColumn('description_ar', fn(NurseryBranch $item) => $item->translateOrNew('ar')->description ?? '')
            ->addColumn('description_en', fn(NurseryBranch $item) => $item->translateOrNew('en')->description ?? '')
            ->addColumn('address_ar', fn(NurseryBranch $item) => $item->translateOrNew('ar')->address ?? '')
            ->addColumn('address_en', fn(NurseryBranch $item) => $item->translateOrNew('en')->address ?? '')
            ->addColumn('nursery_id', fn(NurseryBranch $item) => $item->nursery_id)
            ->addColumn('nursery_name', fn(NurseryBranch $item) => $item->nursery?->name ?? '-')
            ->addColumn('government_id', fn(NurseryBranch $item) => $item->government_id)
            ->addColumn('government_name', fn(NurseryBranch $item) => $item->government?->name ?? '-')
            ->addColumn('city_id', fn(NurseryBranch $item) => $item->city_id)
            ->addColumn('city_name', fn(NurseryBranch $item) => $item->city?->name ?? '-')
            ->addColumn('employee_id', fn(NurseryBranch $item) => $item->employee_id)
            ->addColumn('employee_name', fn(NurseryBranch $item) => $item->employee?->name ?? '-')
            ->addColumn('status_label', fn(NurseryBranch $item) => $item->status->label())
            ->addColumn('status_badge', fn(NurseryBranch $item) => $item->status->badgeColor())
            ->editColumn('created_at', fn(NurseryBranch $item) => $item->created_at ? $item->created_at->format('d-m-Y') : '-')
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
                          ->orWhereTranslationLike('address', "%{$search}%")
                          ->orWhere('whatsapp', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    //======================
    /**
     * Store a newly created branch.
     */
    public function store(StoreNurseryBranchRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.branches.create'), 403);

        $branch = $this->branchService->createBranch(
            $request->validated(),
            $request->file('image'),
            auth()->id()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Branch created successfully.'),
                'data' => $branch,
            ], 201);
        }

        return redirect()
            ->route('nursery.branches.index')
            ->with('success', __('Branch created successfully.'));
    }

    //======================
    /**
     * Update the specified branch.
     */
    public function update(UpdateNurseryBranchRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.branches.edit'), 403);

        $branch = $this->branchService->updateBranch(
            $id,
            $request->validated(),
            $request->file('image')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Branch updated successfully.'),
                'data' => $branch,
            ]);
        }

        return redirect()
            ->route('nursery.branches.index')
            ->with('success', __('Branch updated successfully.'));
    }

    //======================
    /**
     * Remove the specified branch (Soft Delete).
     */
    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.branches.delete'), 403);

        $this->branchService->deleteBranch($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Branch deleted successfully.'),
            ]);
        }

        return redirect()
            ->route('nursery.branches.index')
            ->with('success', __('Branch deleted successfully.'));
    }

    //======================
    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.branches.toggle-status'), 403);

        $branch = $this->branchService->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'status' => $branch->status->value,
            'badge' => $branch->status->badgeColor(),
        ]);
    }

    //======================
    /**
     * Get cities list by government ID via AJAX for dynamic dropdown.
     */
    public function getCities(int $governmentId): JsonResponse
    {
        abort_if(!auth()->user()->hasAnyPermission(['nursery.branches.view', 'nursery.branches.create', 'nursery.branches.edit']), 403);

        $cities = $this->branchService->getCitiesByGovernment($governmentId);

        return response()->json([
            'success' => true,
            'data' => $cities->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ]),
        ]);
    }
}
