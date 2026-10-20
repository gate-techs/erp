<?php

namespace Modules\Nursery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Nursery\Http\Requests\StorePackageRequest;
use Modules\Nursery\Http\Requests\UpdatePackageRequest;
use Modules\Nursery\Models\Package;
use Modules\Nursery\Services\PackageService;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{
    public function __construct(
        protected PackageService $packageService
    ) {}

    public function index(): View
    {
        abort_if(! auth()->user()->can('nursery.packages.view'), 403);
        return view('nursery::packages.index', [
            'nurseries' => $this->packageService->getNurseries(),
        ]);
    }

    public function create(): View
    {
        abort_if(! auth()->user()->can('nursery.packages.create'), 403);
        return view('nursery::packages.create', [
            'nurseries' => $this->packageService->getNurseries(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('nursery.packages.view'), 403);
        $canEdit = auth()->user()->can('nursery.packages.edit');
        $canDelete = auth()->user()->can('nursery.packages.delete');
        $canToggle = auth()->user()->can('nursery.packages.toggle-status');
        $canView = auth()->user()->can('nursery.packages.view');

        $query = $this->packageService->getFilteredQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image_url', fn (Package $item) => $item->cover_image_url)
            ->addColumn('name_ar', fn (Package $item) => $item->translateOrNew('ar')->name ?? '-')
            ->addColumn('name_en', fn (Package $item) => $item->translateOrNew('en')->name ?? '-')
            ->addColumn('nursery_name', fn (Package $item) => $item->nursery?->name ?? '-')
            ->addColumn('discount_display', function (Package $item) {
                return $item->discount_type === 'percentage'
                    ? $item->discount_value . '%'
                    : number_format((float) $item->discount_value, 3);
            })
            ->addColumn('status_badge', function (Package $item) use ($canToggle) {
                $class = $item->status === 'active' ? 'bg-label-success' : 'bg-label-danger';
                $label = $item->status === 'active' ? __('Active') : __('Inactive');

                if (! $canToggle) {
                    return '<span class="badge ' . $class . '">' . e($label) . '</span>';
                }

                return '<button type="button" class="btn btn-sm ' . $class . ' btn-toggle-package-status border-0" data-id="' . $item->id . '">' . e($label) . '</button>';
            })
            ->addColumn('actions', function (Package $item) use ($canView, $canEdit, $canDelete) {
                $name = e($item->translateOrNew(app()->getLocale())->name ?? $item->translateOrNew('ar')->name ?? '');
                $buttons = [];

                if ($canView) {
                    $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-label-info btn-view-package" data-id="' . $item->id . '" title="' . e(__('View')) . '"><i class="bx bx-show"></i></button>';
                }
                if ($canEdit) {
                    $buttons[] = '<a href="' . route('packages.edit', $item->id) . '" class="btn btn-sm btn-icon btn-label-primary" title="' . e(__('Edit')) . '"><i class="bx bx-edit"></i></a>';
                }
                if ($canDelete) {
                    $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-package" data-id="' . $item->id . '" data-name="' . $name . '" title="' . e(__('Delete')) . '"><i class="bx bx-trash"></i></button>';
                }

                if (empty($buttons)) {
                    return '<span class="text-muted">-</span>';
                }

                return '<div class="d-flex justify-content-center gap-1">' . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['status_badge', 'actions'])
            ->filterColumn('name_ar', fn ($query, $keyword) => $query->whereTranslationLike('name', "%{$keyword}%", 'ar'))
            ->filterColumn('name_en', fn ($query, $keyword) => $query->whereTranslationLike('name', "%{$keyword}%", 'en'))
            ->make(true);
    }

    public function store(StorePackageRequest $request): JsonResponse|RedirectResponse
    {
        abort_if(! auth()->user()->can('nursery.packages.create'), 403);
        $package = $this->packageService->create(
            $request->validated(),
            $request->file('cover_image'),
            $request->file('gallery', []),
            auth()->id()
        );

        return $this->response($request, __('Package created successfully.'), $package, 201);
    }

    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('nursery.packages.view'), 403);
        $package = $this->packageService->find($id);
        abort_unless($package, 404, __('Package not found.'));

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $package->id,
                'nursery_id' => $package->nursery_id,
                'name_ar' => $package->translateOrNew('ar')->name,
                'name_en' => $package->translateOrNew('en')->name,
                'description_ar' => $package->translateOrNew('ar')->description,
                'description_en' => $package->translateOrNew('en')->description,
                'cover_image_url' => $package->cover_image_url,
                'price' => $package->price,
                'discount_type' => $package->discount_type,
                'discount_value' => $package->discount_value,
                'sort_order' => $package->sort_order,
                'status' => $package->status,
                'features' => $package->features->map(fn ($feature) => [
                    'name_ar' => $feature->translateOrNew('ar')->name,
                    'name_en' => $feature->translateOrNew('en')->name,
                ]),
                'images' => $package->images->map(fn ($image) => $image->image_url),
            ],
        ]);
    }

    public function edit(int $id): View
    {
        abort_if(! auth()->user()->can('nursery.packages.edit'), 403);
        $package = $this->packageService->find($id);
        abort_unless($package, 404, __('Package not found.'));

        return view('nursery::packages.edit', [
            'package' => $package,
            'nurseries' => $this->packageService->getNurseries(),
        ]);
    }

    public function update(UpdatePackageRequest $request, int $id): JsonResponse|RedirectResponse
    {
        abort_if(! auth()->user()->can('nursery.packages.edit'), 403);
        $package = $this->packageService->update(
            $id,
            $request->validated(),
            $request->file('cover_image'),
            $request->file('gallery', [])
        );
        abort_unless($package, 404, __('Package not found.'));

        return $this->response($request, __('Package updated successfully.'), $package);
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        abort_if(! auth()->user()->can('nursery.packages.delete'), 403);
        abort_unless($this->packageService->delete($id), 404, __('Package not found.'));
        return $this->response($request, __('Package deleted successfully.'));
    }

    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('nursery.packages.toggle-status'), 403);
        $package = $this->packageService->toggleStatus($id);
        abort_unless($package, 404, __('Package not found.'));

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
        ]);
    }

    protected function response(Request $request, string $message, mixed $data = null, int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
        }

        return redirect()->route('packages.index')->with('success', $message);
    }
}
