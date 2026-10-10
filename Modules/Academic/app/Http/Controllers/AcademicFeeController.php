<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Http\Requests\StoreAcademicFeeRequest;
use Modules\Academic\Http\Requests\UpdateAcademicFeeRequest;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Services\AcademicFeeService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicFeeController extends Controller
{
    public function __construct(
        protected AcademicFeeService $service
    ) {}

    public function index()
    {
        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $branches = NurseryBranch::with('translations')->where('status', 'active')->get();
        $academicYears = AcademicYear::with('translations')->where('status', 'active')->get();
        $academicStages = AcademicStage::with('translations')->where('status', 'active')->get();
        $feeTypes = AcademicFeeType::with('translations')->where('status', 'active')->orderBy('order')->get();
        $billingPeriods = AcademicFee::billingPeriods();
        $statuses = AcademicFee::statuses();

        return view('academic::academic-fees.index', compact(
            'institutions',
            'branches',
            'academicYears',
            'academicStages',
            'feeTypes',
            'billingPeriods',
            'statuses'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $request->only([
            'institution_id',
            'branch_id',
            'academic_year_id',
            'academic_stage_id',
            'academic_fee_type_id',
            'billing_period',
            'status',
            'search',
        ]);

        $query = $this->service->getFilteredQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('fee_info', function (AcademicFee $fee) {
                $locale = app()->getLocale();
                $name = $fee->translate($locale)?->name ?? $fee->translate('ar')?->name ?? $fee->name ?? '-';
                $imageUrl = e($fee->image_url);
                $nameSafe = e($name);
                $subName = e($fee->translate($locale === 'ar' ? 'en' : 'ar')?->name ?? '');

                return '
                    <div class="d-flex align-items-center gap-3">
                        <img src="' . $imageUrl . '" alt="' . $nameSafe . '" class="rounded border object-fit-cover shadow-xs" width="42" height="42" onerror="this.src=\'' . asset('assets/img/favicon/favicon.ico') . '\'">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-heading">' . $nameSafe . '</span>
                            <small class="text-muted">' . $subName . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('institution_name', function (AcademicFee $fee) {
                $locale = app()->getLocale();
                return $fee->institution
                    ? ($fee->institution->translate($locale)?->name ?? $fee->institution->translate('ar')?->name ?? $fee->institution->name)
                    : '-';
            })
            ->addColumn('branch_name', function (AcademicFee $fee) {
                $locale = app()->getLocale();
                return $fee->branch
                    ? ($fee->branch->translate($locale)?->name ?? $fee->branch->translate('ar')?->name ?? $fee->branch->name)
                    : '-';
            })
            ->addColumn('period_name', function (AcademicFee $fee) {
                $locale = app()->getLocale();
                return $fee->academicYear
                    ? ($fee->academicYear->translate($locale)?->name ?? $fee->academicYear->translate('ar')?->name ?? $fee->academicYear->name ?? $fee->academicYear->code)
                    : '-';
            })
            ->addColumn('stage_name', function (AcademicFee $fee) {
                $locale = app()->getLocale();
                return $fee->stage
                    ? ($fee->stage->translate($locale)?->name ?? $fee->stage->translate('ar')?->name ?? $fee->stage->name ?? $fee->stage->code)
                    : '-';
            })
            ->addColumn('fee_type_name', function (AcademicFee $fee) {
                $locale = app()->getLocale();
                return $fee->feeType
                    ? ($fee->feeType->translate($locale)?->name ?? $fee->feeType->translate('ar')?->name ?? $fee->feeType->code ?? '-')
                    : '-';
            })
            ->addColumn('billing_period_badge', function (AcademicFee $fee) {
                $labels = [
                    AcademicFee::PERIOD_ONE_TIME => ['label' => __('One-Time'), 'class' => 'bg-label-info'],
                    AcademicFee::PERIOD_MONTHLY  => ['label' => __('Monthly'), 'class' => 'bg-label-primary'],
                    AcademicFee::PERIOD_TERM     => ['label' => __('Term / Semester'), 'class' => 'bg-label-warning'],
                    AcademicFee::PERIOD_ANNUALLY => ['label' => __('Annually'), 'class' => 'bg-label-success'],
                ];

                $cfg = $labels[$fee->billing_period] ?? ['label' => e($fee->billing_period), 'class' => 'bg-label-secondary'];

                return '<span class="badge ' . $cfg['class'] . '">' . $cfg['label'] . '</span>';
            })
            ->addColumn('amount_formatted', function (AcademicFee $fee) {
                return '<span class="fw-bold text-success font-16">' . number_format($fee->amount, 2) . '</span>';
            })
            ->addColumn('status_badge', function (AcademicFee $fee) {
                $isActive = $fee->status === AcademicFee::STATUS_ACTIVE;
                $badgeClass = $isActive ? 'btn-success' : 'btn-label-danger';
                $label = $isActive ? __('Active') : __('Inactive');

                return '<button type="button" class="btn btn-xs ' . $badgeClass . ' btn-toggle-academic-fee-status" data-id="' . $fee->id . '">'
                    . e($label) .
                '</button>';
            })
            ->addColumn('actions', function (AcademicFee $fee) {
                $title = $fee->translate(app()->getLocale())?->name ?? $fee->translate('ar')?->name ?? '';

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-academic-fee" data-id="' . $fee->id . '" title="' . e(__('View Details')) . '">
                            <i class="icon-base bx bx-show"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-fee" data-id="' . $fee->id . '" title="' . e(__('Edit')) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-fee" data-id="' . $fee->id . '" data-name="' . e($title) . '" title="' . e(__('Delete')) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['fee_info', 'billing_period_badge', 'amount_formatted', 'status_badge', 'actions'])
            ->toJson();
    }

    public function store(StoreAcademicFeeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $image = $request->file('image');

        $fee = $this->service->create($data, $image);

        return response()->json([
            'success' => true,
            'message' => __('Academic fee created successfully.'),
            'data'    => $fee,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $fee = $this->service->find($id);

        if (! $fee) {
            return response()->json([
                'success' => false,
                'message' => __('Academic fee not found.'),
            ], 404);
        }

        $locale = app()->getLocale();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                   => $fee->id,
                'institution_id'       => $fee->institution_id,
                'institution_name'     => $fee->institution?->translate($locale)?->name ?? $fee->institution?->name ?? '-',
                'branch_id'            => $fee->branch_id,
                'branch_name'          => $fee->branch?->translate($locale)?->name ?? $fee->branch?->name ?? '-',
                'academic_year_id'     => $fee->academic_year_id,
                'academic_year_name'   => $fee->academicYear?->translate($locale)?->name ?? $fee->academicYear?->code ?? '-',
                'academic_stage_id'    => $fee->academic_stage_id,
                'academic_stage_name'  => $fee->stage?->translate($locale)?->name ?? $fee->stage?->code ?? '-',
                'academic_fee_type_id' => $fee->academic_fee_type_id,
                'fee_type_name'        => $fee->feeType?->translate($locale)?->name ?? $fee->feeType?->code ?? '-',
                'billing_period'       => $fee->billing_period,
                'billing_period_text'  => match ($fee->billing_period) {
                    AcademicFee::PERIOD_ONE_TIME => __('One-Time'),
                    AcademicFee::PERIOD_MONTHLY  => __('Monthly'),
                    AcademicFee::PERIOD_TERM     => __('Term / Semester'),
                    AcademicFee::PERIOD_ANNUALLY => __('Annually'),
                    default => $fee->billing_period
                },
                'amount'               => $fee->amount,
                'amount_formatted'     => number_format($fee->amount, 2),
                'image_url'            => $fee->image_url,
                'status'               => $fee->status,
                'name_ar'              => $fee->translate('ar')?->name ?? '',
                'name_en'              => $fee->translate('en')?->name ?? '',
                'description_ar'       => $fee->translate('ar')?->description ?? '',
                'description_en'       => $fee->translate('en')?->description ?? '',
            ],
        ]);
    }

    public function update(UpdateAcademicFeeRequest $request, int $id): JsonResponse
    {
        $fee = $this->service->find($id);

        if (! $fee) {
            return response()->json([
                'success' => false,
                'message' => __('Academic fee not found.'),
            ], 404);
        }

        $data = $request->validated();
        $image = $request->file('image');

        $updated = $this->service->update($id, $data, $image);

        return response()->json([
            'success' => true,
            'message' => __('Academic fee updated successfully.'),
            'data'    => $updated,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $fee = $this->service->find($id);

        if (! $fee) {
            return response()->json([
                'success' => false,
                'message' => __('Academic fee not found.'),
            ], 404);
        }

        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic fee deleted successfully.'),
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $fee = $this->service->find($id);

        if (! $fee) {
            return response()->json([
                'success' => false,
                'message' => __('Academic fee not found.'),
            ], 404);
        }

        $updated = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'data'    => [
                'status' => $updated->status,
            ],
        ]);
    }

    public function getBranches(int $institutionId): JsonResponse
    {
        $branches = NurseryBranch::with('translations')
            ->where('nursery_id', $institutionId)
            ->where('status', 'active')
            ->get();

        $data = $branches->map(function ($branch) {
            $locale = app()->getLocale();
            return [
                'id'   => $branch->id,
                'name' => $branch->translate($locale)?->name ?? $branch->translate('ar')?->name ?? $branch->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function getStages(int $institutionId): JsonResponse
    {
        $stages = AcademicStage::with('translations')
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->get();

        $data = $stages->map(function ($stage) {
            $locale = app()->getLocale();
            return [
                'id'   => $stage->id,
                'name' => $stage->translate($locale)?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function getYears(int $institutionId): JsonResponse
    {
        $years = AcademicYear::with('translations')
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->get();

        $data = $years->map(function ($year) {
            $locale = app()->getLocale();
            return [
                'id'   => $year->id,
                'name' => $year->translate($locale)?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
