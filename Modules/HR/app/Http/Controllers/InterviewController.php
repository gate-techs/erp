<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\EvaluationRecommendation;
use Modules\HR\Enums\InterviewMode;
use Modules\HR\Enums\InterviewStatus;
use Modules\HR\Enums\InterviewType;
use Modules\HR\Http\Requests\StoreEvaluationRequest;
use Modules\HR\Http\Requests\StoreInterviewRequest;
use Modules\HR\Http\Requests\UpdateInterviewRequest;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Interview;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\JobOpening;
use Modules\HR\Repositories\Contracts\InterviewRepositoryInterface;
use Modules\HR\Services\InterviewService;
use Yajra\DataTables\Facades\DataTables;

class InterviewController extends Controller
{
    public function __construct(
        protected InterviewRepositoryInterface $repository,
        protected InterviewService $service
    ) {}

    /**
     * Display interviews list with KPIs and calendar overview.
     */
    public function index(Request $request): View
    {
        abort_if(! auth()->user()->can('hr.interviews.view'), 403);

        $selectedOpeningId = $request->filled('job_opening_id') ? (int) $request->job_opening_id : null;
        $selectedStatus    = $request->get('status');
        $selectedDate      = $request->get('date');

        $jobOpenings = JobOpening::with('translations')->latest()->get();
        $kpis = $this->repository->getInterviewKpis($selectedOpeningId);
        $upcoming = $this->repository->getUpcomingInterviews(6);
        $types = InterviewType::cases();
        $statuses = InterviewStatus::cases();

        return view('hr::interviews.index', compact(
            'jobOpenings',
            'selectedOpeningId',
            'selectedStatus',
            'selectedDate',
            'kpis',
            'upcoming',
            'types',
            'statuses'
        ));
    }

    /**
     * Server-side DataTables JSON response.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.interviews.view'), 403);

        $filters = [
            'job_opening_id' => $request->get('job_opening_id'),
            'status'         => $request->get('status'),
            'type'           => $request->get('type'),
            'mode'           => $request->get('mode'),
            'date'           => $request->get('date'),
        ];

        $query = $this->repository->getDataTableQuery($filters);
        $locale = app()->getLocale();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('candidate_info', function (Interview $item) {
                $c = $item->candidate;
                if (!$c) return '-';

                $title = $c->current_job_title ? '<small class="text-heading d-block fw-semibold">' . e($c->current_job_title) . '</small>' : '';
                $phone = $c->phone ? '<small class="text-muted d-block"><i class="bx bx-phone font-11 me-1"></i>' . e($c->phone) . '</small>' : '';

                return '<div>
                            <a href="' . route('hr.job-applications.show', $item->job_application_id) . '" class="fw-bold text-heading text-hover-primary">' . e($c->full_name) . '</a>
                            <small class="text-muted d-block">' . e($c->email) . '</small>
                            ' . $title . '
                            ' . $phone . '
                        </div>';
            })
            ->addColumn('opening_info', function (Interview $item) use ($locale) {
                $opening = $item->jobOpening;
                if (!$opening) return '-';

                $title = $opening->translate($locale)?->title ?? $opening->translate('ar')?->title ?? $opening->translate('en')?->title ?? $opening->title;
                return '<div class="d-flex flex-column">
                            <span class="badge bg-label-primary rounded-pill px-2 py-0.5 font-11 fw-semibold w-fit mb-1">' . e($opening->opening_code) . '</span>
                            <span class="fw-semibold text-heading small">' . e($title) . '</span>
                        </div>';
            })
            ->addColumn('interview_type_mode', function (Interview $item) {
                // Round pill
                $roundBadge = '<span class="badge bg-label-dark rounded-pill px-2 py-0.5 font-10 fw-bold">'
                    . __('Round') . ' #' . $item->interview_round
                    . '</span>';

                // Type row  — with a muted "Type:" prefix
                $typeBadge = '<span class="badge ' . $item->type->badgeClass() . ' rounded-pill px-2 py-0.5 font-10 fw-semibold">'
                    . '<i class="' . $item->type->icon() . ' me-1 font-10"></i>'
                    . $item->type->label()
                    . '</span>';

                // Mode row  — with a muted "Mode:" prefix
                $modeBadge = '<span class="badge ' . $item->mode->badgeClass() . ' rounded-pill px-2 py-0.5 font-10 fw-semibold">'
                    . '<i class="' . $item->mode->icon() . ' me-1 font-10"></i>'
                    . $item->mode->label()
                    . '</span>';

                // Location / Meeting link
                $link = '';
                if ($item->mode === InterviewMode::ONLINE && $item->meeting_link) {
                    $link = '<div class="mt-1"><a href="' . e($item->meeting_link) . '" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1"><i class="bx bx-video font-12"></i> ' . __('Join Call') . '</a></div>';
                } elseif ($item->location) {
                    $link = '<small class="text-muted d-block mt-1"><i class="bx bx-map font-11 me-1"></i>' . e($item->location) . '</small>';
                }

                return '<div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-1">
                                ' . $roundBadge . '
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <small class="text-muted fw-medium" style="font-size:10px;min-width:34px">' . __('Type') . ':</small>
                                ' . $typeBadge . '
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <small class="text-muted fw-medium" style="font-size:10px;min-width:34px">' . __('Mode') . ':</small>
                                ' . $modeBadge . '
                            </div>
                            ' . $link . '
                        </div>';
            })
            ->addColumn('schedule_info', function (Interview $item) {
                $date = $item->scheduled_at->format('Y-m-d');
                $time = $item->scheduled_at->format('h:i A');
                $dur  = $item->duration_minutes . ' ' . __('min');

                return '<div>
                            <span class="fw-semibold text-heading font-13 d-block"><i class="bx bx-calendar me-1 text-primary"></i>' . $date . '</span>
                            <small class="text-muted"><i class="bx bx-time-five me-1"></i>' . $time . ' (' . $dur . ')</small>
                        </div>';
            })
            ->addColumn('interviewers_list', function (Interview $item) {

                $interviewers = $item->interviewers;
                if ($interviewers->isEmpty()) return '<span class="text-muted small">-</span>';

                $html = '<div class="d-flex flex-wrap gap-1">';
                foreach ($interviewers as $emp) {
                    $empName = $emp->translate(app()->getLocale())?->name ?? $emp->name;
                    $html .= '<span class="badge bg-label-secondary rounded-pill font-10 px-2 py-0.5">' . e($empName) . '</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('score_rating', function (Interview $item) {
                if ($item->overall_score) {
                    $stars = '';
                    $rounded = (int) round($item->overall_score);
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= $i <= $rounded ? '<i class="bx bxs-star text-warning"></i>' : '<i class="bx bx-star text-muted"></i>';
                    }
                    return '<div class="fw-bold font-13 text-heading">' . number_format($item->overall_score, 1) . ' / 5.0</div>
                            <div class="font-11">' . $stars . '</div>';
                }

                return '<span class="badge bg-light text-muted border font-11">' . __('Not Evaluated') . '</span>';
            })
            ->addColumn('status_badge', function (Interview $item) {
                return '<span class="badge rounded-pill ' . $item->status->badgeClass() . ' px-2.5 py-1 d-inline-flex align-items-center gap-1 font-11 fw-semibold">
                            <i class="' . $item->status->icon() . ' font-12"></i>
                            <span>' . $item->status->label() . '</span>
                        </span>';
            })
            ->addColumn('actions', function (Interview $item) {
                $user = auth()->user();

                $btnShow = '';
                if ($user->can('hr.interviews.view')) {
                    $btnShow = '<a href="' . route('hr.interviews.show', $item->id) . '" class="btn btn-sm btn-icon btn-outline-secondary" title="' . __('View Details') . '">
                                    <i class="bx bx-show font-16"></i>
                                </a>';
                }

                $btnEdit = '';
                if ($user->can('hr.interviews.edit')) {
                    $btnEdit = '<a href="' . route('hr.interviews.edit', $item->id) . '" class="btn btn-sm btn-icon btn-outline-warning" title="' . __('Edit Interview') . '">
                                    <i class="bx bx-edit font-16"></i>
                                </a>';
                }

                $btnEvaluate = '';
                if ($user->can('hr.interviews.evaluate')) {
                    $btnEvaluate = '<a href="' . route('hr.interviews.evaluate', $item->id) . '" class="btn btn-sm btn-icon btn-outline-primary" title="' . __('Scorecard / Evaluate') . '">
                                        <i class="bx bx-check-square font-16"></i>
                                    </a>';
                }

                $btnCancel = '';
                if ($item->status === InterviewStatus::SCHEDULED && $user->can('hr.interviews.cancel')) {
                    $btnCancel = '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-cancel-interview" data-id="' . $item->id . '" title="' . __('Cancel Interview') . '">
                                    <i class="bx bx-x font-16"></i>
                                  </button>';
                }

                $btnDelete = '';
                if ($user->can('hr.interviews.delete')) {
                    $btnDelete = '<form action="' . route('hr.interviews.destroy', $item->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'' . __('Are you sure?') . '\')">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="' . __('Delete') . '">
                                        <i class="bx bx-trash font-16"></i>
                                    </button>
                                  </form>';
                }

                $buttons = $btnShow . $btnEdit . $btnEvaluate . $btnCancel . $btnDelete;
                return $buttons ? '<div class="d-flex align-items-center justify-content-center gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['candidate_info', 'opening_info', 'interview_type_mode', 'schedule_info', 'interviewers_list', 'score_rating', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show the interview scheduling form.
     */
    public function create(Request $request): View
    {
        abort_if(! auth()->user()->can('hr.interviews.create'), 403);

        $jobApplicationId = $request->get('job_application_id');
        $selectedApplication = $jobApplicationId ? JobApplication::with(['candidate', 'jobOpening.translations'])->find($jobApplicationId) : null;

        // Applications in screening or interview or new stages
        $applications = JobApplication::with(['candidate', 'jobOpening.translations'])
            ->where('status', 'active')
            ->latest()
            ->get();

        // Staff for interviewers
        $employees = Employee::with('translations')->whereNull('deleted_at')->get();

        $types = InterviewType::cases();
        $modes = InterviewMode::cases();

        return view('hr::interviews.create', compact(
            'selectedApplication',
            'applications',
            'employees',
            'types',
            'modes'
        ));
    }

    /**
     * Store a scheduled interview session.
     */
    public function store(StoreInterviewRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.interviews.create'), 403);

        $interview = $this->service->scheduleInterview($request->validated());

        return redirect()->route('hr.interviews.show', $interview->id)
            ->with('success', __('Interview scheduled successfully and candidate stage updated in pipeline.'));
    }

    /**
     * Display interview dossier and evaluations.
     */
    public function show(int $id): View
    {
        abort_if(! auth()->user()->can('hr.interviews.view'), 403);

        $interview = $this->repository->findWithRelations($id);
        if (!$interview) {
            abort(404, __('Interview session not found.'));
        }

        $employees = Employee::with('translations')->get();
        $recommendations = EvaluationRecommendation::cases();

        return view('hr::interviews.show', compact('interview', 'employees', 'recommendations'));
    }

    /**
     * Show form to edit an interview session (mode, schedule, committee, status).
     */
    public function edit(int $id): View
    {
        abort_if(! auth()->user()->can('hr.interviews.edit'), 403);

        $interview = $this->repository->findWithRelations($id);
        if (!$interview) {
            abort(404, __('Interview session not found.'));
        }

        $employees = Employee::with('translations')->whereNull('deleted_at')->get();
        $types = InterviewType::cases();
        $modes = InterviewMode::cases();
        $statuses = InterviewStatus::cases();

        return view('hr::interviews.edit', compact(
            'interview',
            'employees',
            'types',
            'modes',
            'statuses'
        ));
    }

    /**
     * Update an interview session.
     */
    public function update(int $id, UpdateInterviewRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.interviews.edit'), 403);

        $interview = Interview::findOrFail($id);
        $this->service->updateInterview($interview, $request->validated());

        return redirect()->route('hr.interviews.show', $interview->id)
            ->with('success', __('Interview session updated successfully.'));
    }

    /**
     * Show the evaluation scorecard form.
     */
    public function evaluate(int $id, Request $request): View
    {
        abort_if(! auth()->user()->can('hr.interviews.evaluate'), 403);

        $interview = $this->repository->findWithRelations($id);
        if (!$interview) {
            abort(404);
        }

        $employees = $interview->interviewers;
        $recommendations = EvaluationRecommendation::cases();

        return view('hr::interviews.evaluate', compact('interview', 'employees', 'recommendations'));
    }

    /**
     * Store candidate evaluation scorecard.
     */
    public function storeEvaluation(int $id, StoreEvaluationRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.interviews.evaluate'), 403);

        $interview = Interview::findOrFail($id);
        $interviewerId = (int) $request->interviewer_id;

        $this->service->submitEvaluation($interview, $interviewerId, $request->validated());

        return redirect()->route('hr.interviews.show', $interview->id)
            ->with('success', __('Evaluation scorecard submitted successfully and rating updated.'));
    }

    /**
     * Cancel an interview.
     */
    public function cancel(int $id, Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.interviews.cancel'), 403);

        $interview = Interview::findOrFail($id);
        $reason = $request->get('reason');

        $this->service->cancelInterview($interview, $reason);

        return response()->json([
            'success' => true,
            'message' => __('Interview cancelled successfully.'),
        ]);
    }

    /**
     * Delete an interview.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.interviews.delete'), 403);

        $interview = Interview::findOrFail($id);
        $interview->delete();

        return redirect()->route('hr.interviews.index')
            ->with('success', __('Interview record deleted successfully.'));
    }
}
