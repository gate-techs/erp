<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Http\Requests\StoreEntityRequest;
use Modules\Accounting\Http\Requests\UpdateEntityRequest;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Services\EntityService;
use Modules\Nursery\Models\Nursery;
use Yajra\DataTables\Facades\DataTables;

class EntityController extends Controller
{
    //==================================================
    public function __construct(
        protected EntityService $entityService
    ) {}

    //==================================================
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $allNurseries = Nursery::with('translations')->latest('id')->get();
        $availableNurseries = $this->entityService->getAvailableNurseries();
        $existingEntities = Entity::with('translations')->orderBy('id')->get();

        $types = [
            'nursery' => __('Nursery'),
            'other'   => __('Other'),
        ];

        $nextCode = $this->entityService->generateEntityCode();

        return view('accounting::entities.index', [
            'allNurseries'       => $allNurseries,
            'availableNurseries' => $availableNurseries,
            'existingEntities'   => $existingEntities,
            'defaultCurrency'    => 'KWD',
            'nextCode'           => $nextCode,
            'types'              => $types,
        ]);
    }

    //==================================================
    /**
     * Server-side DataTables endpoint for entities.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->entityService->getDataTableQuery($request->all())
            ->with(['translations', 'nursery.translations', 'creator']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(Entity $item) => $item->translateOrNew('ar')->name ?? '-')
            ->addColumn('name_en', fn(Entity $item) => $item->translateOrNew('en')->name ?? '-')
            ->addColumn('description_ar', fn(Entity $item) => $item->translateOrNew('ar')->description ?? '')
            ->addColumn('description_en', fn(Entity $item) => $item->translateOrNew('en')->description ?? '')
            ->addColumn('entity_info', function (Entity $item) {
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name ?? $item->translate('ar')?->name ?? $item->translate('en')?->name ?? '-';
                $code = e($item->code);

                $iconClass = match ($item->type) {
                    'nursery' => 'bx-building text-primary',
                    default   => 'bx-briefcase-alt-2 text-secondary',
                };

                return '
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-label-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded">
                            <i class="icon-base bx ' . $iconClass . ' font-20"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-heading fw-bold fs-6">' . e($name) . '</span>
                            <small class="text-muted">' . $code . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('type_badge', function (Entity $item) {
                $badges = [
                    'nursery' => ['class' => 'bg-label-primary', 'label' => __('Nursery')],
                    'other'   => ['class' => 'bg-label-secondary', 'label' => __('Other')],
                ];

                $cfg = $badges[$item->type] ?? ['class' => 'bg-label-secondary', 'label' => $item->type];

                return '<span class="badge ' . $cfg['class'] . '">' . e($cfg['label']) . '</span>';
            })
            ->addColumn('nursery_info', function (Entity $item) {
                if ($item->nursery) {
                    $locale = app()->getLocale();
                    $nurseryName = $item->nursery->translate($locale)?->name ?? $item->nursery->translate('ar')?->name ?? $item->nursery->name;
                    return '
                        <span class="d-flex align-items-center gap-1 text-heading">
                            <i class="icon-base bx bx-home-heart text-primary font-16"></i>
                            <span>' . e($nurseryName) . '</span>
                        </span>
                    ';
                }

                return '<span class="badge bg-label-secondary"><i class="icon-base bx bx-minus me-1"></i>' . __('Independent') . '</span>';
            })
            ->addColumn('currency_badge', function (Entity $item) {
                return '<span class="badge bg-label-dark fw-bold">' . e($item->currency) . '</span>';
            })
            ->addColumn('status_badge', function (Entity $item) {
                if ($item->status === 'active') {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-check-circle me-1 font-12"></i>' . __('Active') . '</span>';
                }

                return '<span class="badge bg-label-danger d-inline-flex align-items-center"><i class="icon-base bx bx-x-circle me-1 font-12"></i>' . __('Inactive') . '</span>';
            })
            ->editColumn('created_at', fn(Entity $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (Entity $item) {
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-entity"
                            data-id="' . $item->id . '"
                            title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-entity"
                            data-id="' . $item->id . '"
                            data-name="' . e($item->name ?? $item->code) . '"
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['entity_info', 'type_badge', 'nursery_info', 'currency_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    //==================================================
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntityRequest $request): JsonResponse|RedirectResponse
    {
        $entity = $this->entityService->createEntity(
            $request->validated(),
            auth()->id()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Entity created successfully.'),
                'data'    => $entity,
            ], 201);
        }

        return redirect()
            ->route('accounting.entities.index')
            ->with('success', __('Entity created successfully.'));
    }

    //==================================================
    /**
     * Show entity details (JSON).
     */
    public function show(int $id): JsonResponse
    {
        $entity = $this->entityService->getEntityById($id);

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => __('Entity not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $entity->id,
                'code'           => $entity->code,
                'type'           => $entity->type,
                'currency'       => $entity->currency,
                'status'         => $entity->status,
                'nursery_id'     => $entity->nursery_id,
                'name_ar'        => $entity->translateOrNew('ar')->name ?? '',
                'name_en'        => $entity->translateOrNew('en')->name ?? '',
                'description_ar' => $entity->translateOrNew('ar')->description ?? '',
                'description_en' => $entity->translateOrNew('en')->description ?? '',
            ],
        ]);
    }

    //==================================================
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $entity = $this->entityService->updateEntity(
            $id,
            $request->validated()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Entity updated successfully.'),
                'data'    => $entity,
            ]);
        }

        return redirect()
            ->route('accounting.entities.index')
            ->with('success', __('Entity updated successfully.'));
    }

    //==================================================
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, Request $request): JsonResponse|RedirectResponse
    {
        $deleted = $this->entityService->deleteEntity($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? __('Entity deleted successfully.') : __('Failed to delete entity.'),
            ], $deleted ? 200 : 422);
        }

        return redirect()
            ->route('accounting.entities.index')
            ->with($deleted ? 'success' : 'error', $deleted ? __('Entity deleted successfully.') : __('Failed to delete entity.'));
    }
}
