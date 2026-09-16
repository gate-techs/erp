<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Models\Department;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class HRSettingController extends Controller
{
    //======================
    /**
     * Entry point for HR Settings, redirects to Administrations.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('hr.settings.administrations.index');
    }

    // =========================================================================
    // 1. Administrations (الإدارات الرئيسية)
    // =========================================================================

    //======================
    public function administrations(): View
    {
        $counts   = $this->getCounts();
        $branches = NurseryBranch::with('translations')->latest('id')->get();

        return view('hr::settings.administrations.index', compact('counts', 'branches'));
    }

    //======================
    public function administrationsData(): JsonResponse
    {
        $query = Department::with(['translations', 'branch.translations'])
            ->whereNull('parent_id')
            ->latest('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(Department $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(Department $item) => $item->translate('en')?->name ?? '-')
            ->addColumn('branch_name', fn(Department $item) => $item->branch?->name ?? '<span class="text-muted">' . __('All Branches') . '</span>')
            ->addColumn('branch_id', fn(Department $item) => $item->branch_id)
            ->editColumn('code', fn(Department $item) => $item->code ?? '-')
            ->editColumn('created_at', fn(Department $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->rawColumns(['branch_name'])
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
                          ->orWhere('code', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    //======================
    public function storeAdministration(Request $request): RedirectResponse
    {
        $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'code'      => 'nullable|string|max:50|unique:departments,code',
            'branch_id' => 'nullable|exists:nursery_branches,id',
        ]);

        Department::create([
            'code'       => $request->input('code'),
            'branch_id'  => $request->input('branch_id'),
            'parent_id'  => null,
            'status'     => 'active',
            'created_by' => auth()->id(),
            'ar' => [
                'name' => $request->input('name_ar'),
            ],
            'en' => [
                'name' => $request->input('name_en'),
            ],
        ]);

        return redirect()->route('hr.settings.administrations.index')
            ->with('success', __('Administration created successfully.'));
    }

    //======================
    public function updateAdministration(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'code'      => 'nullable|string|max:50|unique:departments,code,' . $id,
            'branch_id' => 'nullable|exists:nursery_branches,id',
        ]);

        $admin = Department::whereNull('parent_id')->findOrFail($id);
        $admin->update([
            'code'      => $request->input('code'),
            'branch_id' => $request->input('branch_id'),
            'ar' => [
                'name' => $request->input('name_ar'),
            ],
            'en' => [
                'name' => $request->input('name_en'),
            ],
        ]);

        return redirect()->route('hr.settings.administrations.index')
            ->with('success', __('Administration updated successfully.'));
    }

    //======================
    public function destroyAdministration(int $id): RedirectResponse
    {
        $admin = Department::whereNull('parent_id')->findOrFail($id);
        $admin->delete();

        return redirect()->route('hr.settings.administrations.index')
            ->with('success', __('Administration deleted successfully.'));
    }

    // =========================================================================
    // 2. Departments (الأقسام الفرعية التابعة للإدارات)
    // =========================================================================

    //======================
    public function departments(): View
    {
        $counts          = $this->getCounts();
        $branches        = NurseryBranch::with('translations')->latest('id')->get();
        $administrations = Department::with('translations')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->get();

        return view('hr::settings.departments.index', compact('counts', 'administrations', 'branches'));
    }

    //======================
    public function departmentsData(): JsonResponse
    {
        $query = Department::with(['translations', 'parent.translations', 'branch.translations'])
            ->whereNotNull('parent_id')
            ->latest('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(Department $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(Department $item) => $item->translate('en')?->name ?? '-')
            ->addColumn('parent_name', fn(Department $item) => $item->parent?->name ?? '-')
            ->addColumn('parent_id', fn(Department $item) => $item->parent_id)
            ->addColumn('branch_name', fn(Department $item) => $item->branch?->name ?? '<span class="text-muted">' . __('All Branches') . '</span>')
            ->addColumn('branch_id', fn(Department $item) => $item->branch_id)
            ->editColumn('code', fn(Department $item) => $item->code ?? '-')
            ->editColumn('created_at', fn(Department $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->rawColumns(['parent_name', 'branch_name'])
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
                          ->orWhere('code', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    //======================
    public function storeDepartment(Request $request): RedirectResponse
    {
        $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'parent_id' => 'required|exists:departments,id',
            'branch_id' => 'nullable|exists:nursery_branches,id',
            'code'      => 'nullable|string|max:50|unique:departments,code',
        ]);

        Department::create([
            'code'       => $request->input('code'),
            'parent_id'  => $request->input('parent_id'),
            'branch_id'  => $request->input('branch_id'),
            'status'     => 'active',
            'created_by' => auth()->id(),
            'ar' => [
                'name' => $request->input('name_ar'),
            ],
            'en' => [
                'name' => $request->input('name_en'),
            ],
        ]);

        return redirect()->route('hr.settings.departments.index')
            ->with('success', __('Department created successfully.'));
    }

    //======================
    public function updateDepartment(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'parent_id' => 'required|exists:departments,id',
            'branch_id' => 'nullable|exists:nursery_branches,id',
            'code'      => 'nullable|string|max:50|unique:departments,code,' . $id,
        ]);

        $dept = Department::whereNotNull('parent_id')->findOrFail($id);
        $dept->update([
            'code'      => $request->input('code'),
            'parent_id' => $request->input('parent_id'),
            'branch_id' => $request->input('branch_id'),
            'ar' => [
                'name' => $request->input('name_ar'),
            ],
            'en' => [
                'name' => $request->input('name_en'),
            ],
        ]);

        return redirect()->route('hr.settings.departments.index')
            ->with('success', __('Department updated successfully.'));
    }

    //======================
    public function destroyDepartment(int $id): RedirectResponse
    {
        $dept = Department::whereNotNull('parent_id')->findOrFail($id);
        $dept->delete();

        return redirect()->route('hr.settings.departments.index')
            ->with('success', __('Department deleted successfully.'));
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    //======================
    protected function getCounts(): array
    {
        return [
            'administrations' => Department::whereNull('parent_id')->count(),
            'departments'     => Department::whereNotNull('parent_id')->count(),
            'job_titles'      => \Modules\HR\Models\JobTitle::count(),
        ];
    }
}
