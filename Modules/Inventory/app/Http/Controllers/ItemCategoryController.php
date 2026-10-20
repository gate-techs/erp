<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Inventory\Http\Requests\StoreItemCategoryRequest;
use Modules\Inventory\Http\Requests\UpdateItemCategoryRequest;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Services\ItemCategoryService;
use Yajra\DataTables\Facades\DataTables;

class ItemCategoryController extends Controller
{
    public function __construct(
        protected ItemCategoryService $service
    ) {}

    //------------------------------------------
    public function index(): View
    {
        abort_if(! auth()->user()->can('inventory.categories.view'), 403);

        return view('inventory::categories.index', [
            'parentCategories' => $this->service->getAll(['status' => 'active']),
        ]);
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.categories.view'), 403);

        $query = $this->service->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn (ItemCategory $category) => $category->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn (ItemCategory $category) => $category->translate('en')?->name ?? '-')
            ->addColumn('parent_name', fn (ItemCategory $category) => $category->parent?->name ?? __('Root Category'))
            ->addColumn('type_badge', fn (ItemCategory $category) => $category->parent_id
                ? '<span class="badge bg-label-info">' . __('Subcategory') . '</span>'
                : '<span class="badge bg-label-primary">' . __('Main Category') . '</span>')
            ->addColumn('status_badge', function (ItemCategory $category) {
                $active = $category->status === 'active';
                $class = $active ? 'success' : 'danger';
                $label = $active ? __('Active') : __('Inactive');
                $canToggle = auth()->user()->can('inventory.categories.toggle-status');
                $actionClass = $canToggle ? 'btn-toggle-category-status' : 'pe-none opacity-75';

                return '<button type="button" class="btn btn-sm bg-label-' . $class . ' border-0 ' . $actionClass . '" data-id="' . $category->id . '" ' . ($canToggle ? '' : 'disabled') . '>' . e($label) . '</button>';
            })
            ->addColumn('actions', function (ItemCategory $category) {
                $name = $category->translate(app()->getLocale())?->name
                    ?? $category->translate('ar')?->name
                    ?? $category->code;
                $user = auth()->user();
                $buttons = '';

                if ($user->can('inventory.categories.edit')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-category" data-id="' . $category->id . '" title="' . __('Edit') . '"><i class="icon-base bx bx-edit-alt"></i></button> ';
                }
                if ($user->can('inventory.categories.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-category" data-id="' . $category->id . '" data-name="' . e($name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                return '<div class="d-inline-flex gap-1">' . ($buttons ?: '<span class="text-muted small">-</span>') . '</div>';
            })
            ->filterColumn('name_ar', fn ($query, $keyword) => $query->whereTranslationLike('name', "%{$keyword}%", 'ar'))
            ->filterColumn('name_en', fn ($query, $keyword) => $query->whereTranslationLike('name', "%{$keyword}%", 'en'))
            ->editColumn('created_at', fn (ItemCategory $category) => $category->created_at?->format('d-m-Y') ?? '-')
            ->rawColumns(['type_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    public function store(StoreItemCategoryRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.categories.create'), 403);

        $category = $this->service->create($request->validated(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Item category created successfully.'), 'data' => $category], 201);
        }

        return redirect()->route('inventory.categories.index')->with('success', __('Item category created successfully.'));
    }

    //------------------------------------------
    public function show(Request $request, int $id): JsonResponse|View
    {
        abort_if(! auth()->user()->can('inventory.categories.view'), 403);

        $category = $this->service->getById($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $category]);
        }

        return view('inventory::categories.show', compact('category'));
    }

    //------------------------------------------
    public function update(UpdateItemCategoryRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.categories.edit'), 403);

        $category = $this->service->update($id, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Item category updated successfully.'), 'data' => $category]);
        }

        return redirect()->route('inventory.categories.index')->with('success', __('Item category updated successfully.'));
    }

    //------------------------------------------
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.categories.delete'), 403);

        $this->service->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Item category deleted successfully.')]);
        }

        return redirect()->route('inventory.categories.index')->with('success', __('Item category deleted successfully.'));
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.categories.toggle-status'), 403);

        $category = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'status' => $category->status,
        ]);
    }
}
