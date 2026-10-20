<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobOpeningStatus;
use Modules\HR\Enums\PostingType;
use Modules\HR\Enums\WorkplaceType;
use Modules\HR\Http\Requests\StoreJobOpeningRequest;
use Modules\HR\Http\Requests\UpdateJobOpeningRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobOpening;
use Modules\HR\Models\JobTitle;
use Modules\HR\Services\JobOpeningService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class JobOpeningController extends Controller
{
    public function __construct(
        protected JobOpeningService $openingService
    ) {}

    /**
     * Display a listing of job openings.
     */
    public function index(): View
    {
        abort_if(! auth()->user()->can('hr.job-openings.view'), 403);

        $kpis           = $this->openingService->getKPIs();
        $departments    = Department::with('translations')->orderBy('id')->get();
        $branches       = NurseryBranch::with('translations')->latest('id')->get();
        $statuses       = JobOpeningStatus::cases();
        $empTypes       = EmploymentType::cases();
        $workplaceTypes = WorkplaceType::cases();
        $postingTypes   = PostingType::cases();

        return view('hr::job_openings.index', compact(
            'kpis',
            'departments',
            'branches',
            'statuses',
            'empTypes',
            'workplaceTypes',
            'postingTypes'
        ));
    }

    /**
     * Server-side DataTables JSON response.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.view'), 403);

        $filters = $request->only([
            'status',
            'department_id',
            'branch_id',
            'employment_type',
            'workplace_type',
            'posting_type',
            'search',
        ]);

        $query = $this->openingService->getDataTableQuery($filters);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('opening_info', function (JobOpening $item) {
                $locale    = app()->getLocale();
                $title     = $item->translate($locale)?->title ?? $item->translate('ar')?->title ?? $item->translate('en')?->title ?? $item->title;
                $jobTitle  = $item->jobTitle?->translate($locale)?->name ?? $item->jobTitle?->translate('ar')?->name ?? $item->jobTitle?->name ?? '';
                $deptName  = $item->department?->translate($locale)?->name ?? $item->department?->translate('ar')?->name ?? $item->department?->name ?? '';
                $codeBadge = '<span class="badge bg-label-primary rounded-pill px-2 py-0.5 font-11 fw-semibold">' . e($item->opening_code) . '</span>';
                $reqBadge  = '';
                if ($item->requisition) {
                    $reqBadge = '<a href="' . route('hr.job-requisitions.show', $item->requisition->id) . '" class="badge bg-label-info rounded-pill px-2 py-0.5 font-11 text-decoration-none" title="' . __('Linked Requisition') . '">
                                    <i class="bx bx-link font-11 me-1"></i>' . e($item->requisition->requisition_number) . '
                                 </a>';
                }

                return '<div class="d-flex flex-column">
                            <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                                ' . $codeBadge . '
                                <a href="' . route('hr.job-openings.show', $item->id) . '" class="fw-bold text-heading text-hover-primary">' . e($title) . '</a>
                                ' . $reqBadge . '
                            </div>
                            <small class="text-muted">' . e($jobTitle) . ' &bull; ' . e($deptName) . '</small>
                        </div>';
            })
            ->addColumn('workplace_badge', function (JobOpening $item) {
                $icon = $item->workplace_type->icon() ?? 'bx bx-buildings';
                return '<span class="badge rounded-pill ' . $item->workplace_type->badgeClass() . ' px-2.5 py-1 d-inline-flex align-items-center gap-1 font-11 fw-semibold">
                            <i class="' . $icon . ' font-12"></i>
                            <span>' . $item->workplace_type->label() . '</span>
                        </span>';
            })
            ->addColumn('posting_badge', function (JobOpening $item) {
                return '<span class="badge rounded-pill ' . $item->posting_type->badgeClass() . ' px-2.5 py-1 font-11 fw-semibold">
                            ' . $item->posting_type->label() . '
                        </span>';
            })
            ->addColumn('positions', function (JobOpening $item) {
                $filled = $item->filled_positions;
                $total  = $item->open_positions;
                $rem    = $item->remaining_positions;

                $color = $rem === 0 ? 'text-success' : 'text-primary';
                return '<div class="d-flex flex-column text-start">
                            <span class="fw-bold ' . $color . '">' . $filled . ' / ' . $total . '</span>
                            <small class="text-muted">' . __('Remaining') . ': ' . $rem . '</small>
                        </div>';
            })
            ->addColumn('salary_range', function (JobOpening $item) {
                if ($item->hide_salary) {
                    return '<span class="text-muted fst-italic font-12">' . __('Undisclosed') . '</span>';
                }
                $curr = $item->currency ?? 'KWD';
                if ($item->min_salary && $item->max_salary) {
                    return '<span class="fw-semibold font-12">' . number_format($item->min_salary) . ' - ' . number_format($item->max_salary) . ' <small class="text-muted">' . e($curr) . '</small></span>';
                }
                if ($item->min_salary) {
                    return '<span class="fw-semibold font-12">' . __('From') . ' ' . number_format($item->min_salary) . ' <small class="text-muted">' . e($curr) . '</small></span>';
                }
                return '<span class="text-muted font-12">-</span>';
            })
            ->addColumn('dates', function (JobOpening $item) {
                $pubDate = $item->publish_date ? $item->publish_date->format('Y-m-d') : '-';
                $clsDate = $item->closing_date ? $item->closing_date->format('Y-m-d') : __('Open-ended');

                return '<div class="d-flex flex-column font-12">
                            <span><strong class="text-muted">' . __('Published') . ':</strong> ' . $pubDate . '</span>
                            <span><strong class="text-muted">' . __('Closing') . ':</strong> ' . $clsDate . '</span>
                        </div>';
            })
            ->addColumn('status_badge', function (JobOpening $item) {
                return '<span class="badge rounded-pill ' . $item->status->badgeClass() . ' px-2.5 py-1 d-inline-flex align-items-center gap-1 font-11 fw-semibold">
                            <i class="' . $item->status->icon() . ' font-12"></i>
                            <span>' . $item->status->label() . '</span>
                        </span>';
            })
            ->addColumn('actions', function (JobOpening $item) {
                $user = auth()->user();
                $btnShow = '';
                if ($user->can('hr.job-openings.view')) {
                    $btnShow = '<a href="' . route('hr.job-openings.show', $item->id) . '" class="btn btn-sm btn-icon btn-outline-secondary" title="' . __('View') . '">
                                    <i class="bx bx-show font-16"></i>
                                </a>';
                }

                $btnEdit = '';
                if ($item->status !== JobOpeningStatus::CLOSED && $user->can('hr.job-openings.edit')) {
                    $btnEdit = '<a href="' . route('hr.job-openings.edit', $item->id) . '" class="btn btn-sm btn-icon btn-outline-primary" title="' . __('Edit') . '">
                                    <i class="bx bx-edit font-16"></i>
                                </a>';
                }

                $btnPublish = '';
                if (($item->status === JobOpeningStatus::DRAFT || $item->status === JobOpeningStatus::ON_HOLD) && $user->can('hr.job-openings.publish')) {
                    $btnPublish = '<button type="button" class="btn btn-sm btn-icon btn-outline-success btn-publish-opening" data-id="' . $item->id . '" title="' . __('Publish Opening') . '">
                                      <i class="bx bx-broadcast font-16"></i>
                                   </button>';
                }

                $btnClose = '';
                if ($item->status === JobOpeningStatus::PUBLISHED && $user->can('hr.job-openings.close')) {
                    $btnClose = '<button type="button" class="btn btn-sm btn-icon btn-outline-warning btn-close-opening" data-id="' . $item->id . '" title="' . __('Close Opening') . '">
                                    <i class="bx bx-check-circle font-16"></i>
                                 </button>';
                }

                $btnDelete = '';
                if ($item->status === JobOpeningStatus::DRAFT && $user->can('hr.job-openings.delete')) {
                    $btnDelete = '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-opening" data-id="' . $item->id . '" data-num="' . e($item->opening_code) . '" title="' . __('Delete') . '">
                                    <i class="bx bx-trash font-16"></i>
                                  </button>';
                }

                $buttons = $btnShow . $btnEdit . $btnPublish . $btnClose . $btnDelete;
                return $buttons ? '<div class="d-flex align-items-center gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['opening_info', 'workplace_badge', 'posting_badge', 'positions', 'salary_range', 'dates', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show create job opening form.
     */
    public function create(Request $request): View
    {
        abort_if(! auth()->user()->can('hr.job-openings.create'), 403);

        $departments    = Department::with('translations')->orderBy('id')->get();
        $jobTitles      = JobTitle::with('translations')->orderBy('id')->get();
        $branches       = NurseryBranch::with('translations')->latest('id')->get();
        $requisitions   = $this->openingService->getAvailableRequisitions();
        $empTypes       = EmploymentType::cases();
        $workplaceTypes = WorkplaceType::cases();
        $postingTypes   = PostingType::cases();
        $statuses       = JobOpeningStatus::cases();

        $selectedRequisitionId = $request->input('requisition_id');
        $prefillData           = [];

        if ($selectedRequisitionId) {
            $prefillData = $this->openingService->getOpeningDefaultsFromRequisition((int) $selectedRequisitionId) ?? [];
        }

        return view('hr::job_openings.create', compact(
            'departments',
            'jobTitles',
            'branches',
            'requisitions',
            'empTypes',
            'workplaceTypes',
            'postingTypes',
            'statuses',
            'selectedRequisitionId',
            'prefillData'
        ));
    }

    /**
     * AJAX endpoint to fetch prefill defaults for a requisition.
     */
    public function requisitionDetails(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.create'), 403);

        $requisitionId = $request->input('requisition_id');
        if (!$requisitionId) {
            return response()->json(['success' => false, 'message' => __('Requisition ID required')], 422);
        }

        $data = $this->openingService->getOpeningDefaultsFromRequisition((int) $requisitionId);

        if (!$data) {
            return response()->json(['success' => false, 'message' => __('Requisition not found')], 404);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Store a newly created job opening.
     */
    public function store(StoreJobOpeningRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.create'), 403);

        $opening = $this->openingService->createOpening($request->validated(), auth()->id());

        return redirect()
            ->route('hr.job-openings.show', $opening->id)
            ->with('success', __('Job opening :code created successfully.', ['code' => $opening->opening_code]));
    }

    /**
     * Display the specified job opening.
     */
    public function show(int $id): View
    {
        abort_if(! auth()->user()->can('hr.job-openings.view'), 403);

        $opening = $this->openingService->findById($id);
        abort_if(!$opening, 404);

        return view('hr::job_openings.show', compact('opening'));
    }

    /**
     * Show the edit form for the specified job opening.
     */
    public function edit(int $id): View
    {
        abort_if(! auth()->user()->can('hr.job-openings.edit'), 403);

        $opening = $this->openingService->findById($id);
        abort_if(!$opening, 404);

        $departments    = Department::with('translations')->orderBy('id')->get();
        $jobTitles      = JobTitle::with('translations')->orderBy('id')->get();
        $branches       = NurseryBranch::with('translations')->latest('id')->get();
        $requisitions   = $this->openingService->getAvailableRequisitions();
        $empTypes       = EmploymentType::cases();
        $workplaceTypes = WorkplaceType::cases();
        $postingTypes   = PostingType::cases();
        $statuses       = JobOpeningStatus::cases();

        return view('hr::job_openings.edit', compact(
            'opening',
            'departments',
            'jobTitles',
            'branches',
            'requisitions',
            'empTypes',
            'workplaceTypes',
            'postingTypes',
            'statuses'
        ));
    }

    /**
     * Update the specified job opening.
     */
    public function update(UpdateJobOpeningRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.edit'), 403);

        $opening = $this->openingService->findById($id);
        abort_if(!$opening, 404);

        $updated = $this->openingService->updateOpening($opening, $request->validated(), auth()->id());

        return redirect()
            ->route('hr.job-openings.show', $updated->id)
            ->with('success', __('Job opening :code updated successfully.', ['code' => $updated->opening_code]));
    }

    /**
     * Delete the specified job opening.
     */
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.delete'), 403);

        $opening = $this->openingService->findById($id);
        if (!$opening) {
            return response()->json(['success' => false, 'message' => __('Record not found.')], 404);
        }

        $code = $opening->opening_code;
        $this->openingService->deleteOpening($opening);

        return response()->json([
            'success' => true,
            'message' => __('Job opening :code deleted successfully.', ['code' => $code]),
        ]);
    }

    /**
     * Publish a job opening.
     */
    public function publish(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.publish'), 403);

        $opening = $this->openingService->findById($id);
        if (!$opening) {
            return response()->json(['success' => false, 'message' => __('Record not found.')], 404);
        }

        $this->openingService->publishOpening($opening, auth()->id());

        return response()->json([
            'success' => true,
            'message' => __('Job opening :code has been published successfully.', ['code' => $opening->opening_code]),
        ]);
    }

    /**
     * Close a job opening.
     */
    public function close(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-openings.close'), 403);

        $opening = $this->openingService->findById($id);
        if (!$opening) {
            return response()->json(['success' => false, 'message' => __('Record not found.')], 404);
        }

        $this->openingService->closeOpening($opening);

        return response()->json([
            'success' => true,
            'message' => __('Job opening :code has been closed.', ['code' => $opening->opening_code]),
        ]);
    }
}
