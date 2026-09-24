<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Http\Requests\StoreFiscalYearRequest;
use Modules\Accounting\Http\Requests\UpdateFiscalYearRequest;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Services\FiscalYearService;
use Yajra\DataTables\Facades\DataTables;

class FiscalYearController extends Controller
{
    //===============================
    public function __construct(
        protected FiscalYearService $service
    ) {}

    //===============================
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();

        return view('accounting::fiscal-years.index', [
            'entities' => $entities,
            'currentYear' => date('Y'),
        ]);
    }

    //===============================
    /**
     * Server-side DataTables endpoint for fiscal years.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all())
            ->with(['translations', 'entity.translations', 'creator', 'closer']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('fiscal_year_info', function (FiscalYear $item) {
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name ?? $item->translate('ar')?->name ?? $item->translate('en')?->name ?? '-';
                $startYear = $item->start_date ? $item->start_date->format('Y') : '';
                $endYear = $item->end_date ? $item->end_date->format('Y') : '';
                $yearRange = $startYear === $endYear ? $startYear : "{$startYear} - {$endYear}";

                return '
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-label-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded">
                            <i class="icon-base bx bx-calendar font-20"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-heading fw-bold fs-6">' . e($name) . '</span>
                            <small class="text-muted">' . e($yearRange) . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('entity_info', function (FiscalYear $item) {
                if ($item->entity) {
                    $locale = app()->getLocale();
                    $entityName = $item->entity->translate($locale)?->name ?? $item->entity->translate('ar')?->name ?? $item->entity->code;
                    return '
                        <div class="d-flex flex-column">
                            <span class="d-flex align-items-center gap-1 text-heading fw-semibold">
                                <i class="icon-base bx bx-building text-primary font-16"></i>
                                <span>' . e($entityName) . '</span>
                            </span>
                            <small class="text-muted">' . e($item->entity->code) . '</small>
                        </div>
                    ';
                }

                return '<span class="badge bg-label-secondary">' . __('General') . '</span>';
            })
            ->editColumn('start_date', fn(FiscalYear $item) => $item->start_date ? $item->start_date->format('Y-m-d') : '-')
            ->editColumn('end_date', fn(FiscalYear $item) => $item->end_date ? $item->end_date->format('Y-m-d') : '-')
            ->addColumn('duration_info', function (FiscalYear $item) {
                if ($item->start_date && $item->end_date) {
                    $days = $item->start_date->diffInDays($item->end_date) + 1;
                    return '<span class="badge bg-label-info">' . $days . ' ' . __('Days') . '</span>';
                }
                return '-';
            })
            ->addColumn('status_badge', function (FiscalYear $item) {
                if ($item->status === 'open') {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-lock-open-alt me-1 font-12"></i>' . __('Open') . '</span>';
                }

                return '<span class="badge bg-label-secondary d-inline-flex align-items-center"><i class="icon-base bx bx-lock-alt me-1 font-12"></i>' . __('Closed') . '</span>';
            })
            ->editColumn('created_at', fn(FiscalYear $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (FiscalYear $item) {
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name ?? $item->translate('ar')?->name ?? __('Fiscal Year');
                $toggleTitle = $item->status === 'open' ? __('Close') : __('Reopen');
                $toggleIcon = $item->status === 'open' ? 'bx-lock-alt' : 'bx-lock-open-alt';
                $toggleColor = $item->status === 'open' ? 'btn-outline-warning' : 'btn-outline-success';

                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-fiscal-year"
                            data-id="' . $item->id . '"
                            title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm ' . $toggleColor . ' btn-toggle-status"
                            data-id="' . $item->id . '"
                            data-status="' . $item->status . '"
                            title="' . $toggleTitle . '">
                            <i class="icon-base bx ' . $toggleIcon . '"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-fiscal-year"
                            data-id="' . $item->id . '"
                            data-name="' . e($name) . '"
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['fiscal_year_info', 'entity_info', 'duration_info', 'status_badge', 'actions'])
            ->make(true);
    }

    //===============================
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFiscalYearRequest $request): JsonResponse|RedirectResponse
    {
        $fiscalYear = $this->service->createFiscalYear($request->validated(), auth()->id());

        return $this->respond($request, __('Fiscal year created successfully.'), $fiscalYear, 201);
    }

    //===============================
    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $fiscalYear = $this->service->getFiscalYear($id);

        if (!$fiscalYear) {
            return response()->json([
                'success' => false,
                'message' => __('Fiscal year not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $fiscalYear->id,
                'entity_id' => $fiscalYear->entity_id,
                'name_ar' => $fiscalYear->translateOrNew('ar')->name ?? '',
                'name_en' => $fiscalYear->translateOrNew('en')->name ?? '',
                'description_ar' => $fiscalYear->translateOrNew('ar')->description ?? '',
                'description_en' => $fiscalYear->translateOrNew('en')->description ?? '',
                'start_date' => $fiscalYear->start_date ? $fiscalYear->start_date->format('Y-m-d') : '',
                'end_date' => $fiscalYear->end_date ? $fiscalYear->end_date->format('Y-m-d') : '',
                'status' => $fiscalYear->status,
            ],
        ]);
    }

    //===============================
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFiscalYearRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $fiscalYear = $this->service->updateFiscalYear($id, $request->validated());

        return $this->respond($request, __('Fiscal year updated successfully.'), $fiscalYear);
    }

    //===============================
    /**
     * Toggle status between open and closed.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $fiscalYear = $this->service->changeStatus($id, auth()->id());

        $message = $fiscalYear->status === 'closed'
            ? __('Fiscal year closed successfully.')
            : __('Fiscal year reopened successfully.');

        return $this->respond($request, $message, $fiscalYear);
    }

    //===============================
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $deleted = $this->service->deleteFiscalYear($id);

        return $this->respond($request, __('Fiscal year deleted successfully.'));
    }

    //===============================
    /**
     * Standard response helper for AJAX & standard HTTP redirects.
     */
    protected function respond(Request $request, string $message, mixed $data = null, int $statusCode = 200): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        }

        return redirect()->route('accounting.fiscal-years.index')->with('success', $message);
    }
}
