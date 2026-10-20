<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\ApplicationStatus;
use Modules\HR\Enums\RejectionReason;
use Modules\HR\Http\Requests\StoreJobApplicationRequest;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\JobOpening;
use Modules\HR\Repositories\Contracts\JobApplicationRepositoryInterface;
use Modules\HR\Services\JobApplicationService;
use Yajra\DataTables\Facades\DataTables;

class JobApplicationController extends Controller
{
    public function __construct(
        protected JobApplicationRepositoryInterface $repository,
        protected JobApplicationService $service
    ) {}

    /**
     * Display Kanban Pipeline & Table view.
     */
    public function index(Request $request)
    {
        abort_if(! auth()->user()->can('hr.job-applications.view'), 403);

        $selectedOpeningId = $request->filled('job_opening_id') ? (int) $request->job_opening_id : null;
        $search = $request->get('search');

        $jobOpenings = JobOpening::with(['translations', 'jobTitle'])->latest()->get();
        $kpis = $this->repository->getPipelineKpis($selectedOpeningId);
        $kanbanData = $this->repository->getKanbanData($selectedOpeningId, $search);
        $stages = ApplicationStage::orderedStages();
        $rejectionReasons = RejectionReason::cases();

        return view('hr::job_applications.index', compact(
            'jobOpenings',
            'selectedOpeningId',
            'kpis',
            'kanbanData',
            'stages',
            'rejectionReasons',
            'search'
        ));
    }

    /**
     * Server-side DataTables JSON response.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-applications.view'), 403);

        $filters = [
            'job_opening_id' => $request->get('job_opening_id'),
            'stage'          => $request->get('stage'),
            'status'         => $request->get('status'),
            'rating'         => $request->get('rating'),
        ];

        $query = $this->repository->getDataTableQuery($filters);
        $locale = app()->getLocale();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('candidate_info', function (JobApplication $item) {
                $c = $item->candidate;
                if (!$c) return '-';

                $phone = $c->phone ? '<small class="text-muted d-block"><i class="bx bx-phone font-11 me-1"></i>' . e($c->phone) . '</small>' : '';
                $title = $c->current_job_title ? '<small class="text-heading d-block fw-semibold">' . e($c->current_job_title) . '</small>' : '';

                return '<div>
                            <a href="' . route('hr.job-applications.show', $item->id) . '" class="fw-bold text-heading text-hover-primary">' . e($c->full_name) . '</a>
                            <small class="text-muted d-block">' . e($c->email) . '</small>
                            ' . $title . '
                            ' . $phone . '
                        </div>';
            })
            ->addColumn('opening_info', function (JobApplication $item) use ($locale) {
                $opening = $item->jobOpening;
                if (!$opening) return '-';

                $title = $opening->translate($locale)?->title ?? $opening->translate('ar')?->title ?? $opening->translate('en')?->title ?? $opening->title;
                return '<div class="d-flex flex-column">
                            <span class="badge bg-label-primary rounded-pill px-2 py-0.5 font-11 fw-semibold w-fit mb-1">' . e($opening->opening_code) . '</span>
                            <a href="' . route('hr.job-openings.show', $opening->id) . '" class="fw-semibold text-heading small">' . e($title) . '</a>
                        </div>';
            })
            ->addColumn('stage_badge', function (JobApplication $item) {
                return '<span class="badge rounded-pill ' . $item->stage->badgeClass() . ' px-2.5 py-1 d-inline-flex align-items-center gap-1 font-11 fw-semibold">
                            <i class="' . $item->stage->icon() . ' font-12"></i>
                            <span>' . $item->stage->label() . '</span>
                        </span>';
            })
            ->addColumn('status_badge', function (JobApplication $item) {
                return '<span class="badge rounded-pill ' . $item->status->badgeClass() . ' px-2.5 py-1 font-11 fw-semibold">
                            ' . $item->status->label() . '
                        </span>';
            })
            ->addColumn('salary_rating', function (JobApplication $item) {
                $salary = $item->expected_salary ? '<div class="fw-semibold font-12 text-primary">' . number_format($item->expected_salary) . ' <small class="text-muted">' . e($item->currency ?? 'KWD') . '</small></div>' : '<span class="text-muted font-12">-</span>';
                
                $stars = '';
                if ($item->rating) {
                    $stars = '<div class="text-warning font-11 mt-1">';
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= $i <= $item->rating ? '<i class="bx bxs-star"></i>' : '<i class="bx bx-star text-muted"></i>';
                    }
                    $stars .= '</div>';
                }

                return $salary . $stars;
            })
            ->addColumn('applied_at', function (JobApplication $item) {
                return '<span class="font-12 text-muted">' . $item->created_at->format('Y-m-d H:i') . '</span>';
            })
            ->addColumn('actions', function (JobApplication $item) {
                $user = auth()->user();
                $btnShow = '';
                if ($user->can('hr.job-applications.view')) {
                    $btnShow = '<a href="' . route('hr.job-applications.show', $item->id) . '" class="btn btn-sm btn-icon btn-outline-secondary" title="' . __('View Profile') . '">
                                    <i class="bx bx-show font-16"></i>
                                </a>';
                }

                $btnReject = '';
                if ($item->status !== ApplicationStatus::REJECTED && $user->can('hr.job-applications.reject')) {
                    $btnReject = '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-reject-candidate" data-id="' . $item->id . '" data-name="' . e($item->candidate?->full_name) . '" title="' . __('Reject Candidate') . '">
                                    <i class="bx bx-user-x font-16"></i>
                                  </button>';
                } elseif ($item->status === ApplicationStatus::REJECTED && $user->can('hr.job-applications.reopen')) {
                    $btnReject = '<button type="button" class="btn btn-sm btn-icon btn-outline-success btn-reopen-candidate" data-id="' . $item->id . '" title="' . __('Reopen Application') . '">
                                    <i class="bx bx-undo font-16"></i>
                                  </button>';
                }

                $btnDelete = '';
                if ($user->can('hr.job-applications.delete')) {
                    $btnDelete = '<form action="' . route('hr.job-applications.destroy', $item->id) . '" method="POST" class="d-inline delete-app-form">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger btn-delete-app" title="' . __('Delete') . '">
                                        <i class="bx bx-trash font-16"></i>
                                    </button>
                                  </form>';
                }

                $buttons = $btnShow . $btnReject . $btnDelete;
                return $buttons ? '<div class="d-flex align-items-center justify-content-center gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['candidate_info', 'opening_info', 'stage_badge', 'status_badge', 'salary_rating', 'applied_at', 'actions'])
            ->make(true);
    }

    /**
     * Show form to add a new candidate.
     */
    public function create(Request $request)
    {
        abort_if(! auth()->user()->can('hr.job-applications.create'), 403);

        $selectedOpeningId = $request->get('job_opening_id');
        $jobOpenings = JobOpening::with(['translations', 'jobTitle'])->where('status', 'published')->latest()->get();
        $stages = ApplicationStage::orderedStages();

        return view('hr::job_applications.create', compact('jobOpenings', 'selectedOpeningId', 'stages'));
    }

    /**
     * Store new candidate and application.
     */
    public function store(StoreJobApplicationRequest $request)
    {
        abort_if(! auth()->user()->can('hr.job-applications.create'), 403);

        try {
            $app = $this->service->submitApplication($request->validated(), $request->file('resume'));
            return redirect()
                ->route('hr.job-applications.show', $app->id)
                ->with('success', __('Candidate :name registered successfully with application :code.', [
                    'name' => $app->candidate->full_name,
                    'code' => $app->application_code,
                ]));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display candidate profile & pipeline details.
     */
    public function show(int $id)
    {
        abort_if(! auth()->user()->can('hr.job-applications.view'), 403);

        $application = $this->repository->findById($id);
        if (!$application) {
            abort(404, __('Application not found.'));
        }

        $stages = ApplicationStage::orderedStages();
        $rejectionReasons = RejectionReason::cases();

        return view('hr::job_applications.show', compact('application', 'stages', 'rejectionReasons'));
    }

    /**
     * Update application stage via AJAX (Drag & Drop or button click).
     */
    public function updateStage(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-applications.update-stage'), 403);

        $request->validate([
            'stage' => ['required', 'string', 'in:new,screening,interview,offer,hired'],
        ]);

        try {
            $application = $this->service->updateStage($id, $request->stage);

            return response()->json([
                'success' => true,
                'message' => __('Candidate moved to :stage successfully.', ['stage' => $application->stage->label()]),
                'stage'   => $application->stage->value,
                'badge'   => $application->stage->badgeClass(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject candidate application.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-applications.reject'), 403);

        $request->validate([
            'rejection_reason' => ['required', 'string'],
            'rejection_notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $application = $this->service->rejectApplication($id, $request->rejection_reason, $request->rejection_notes);

            return response()->json([
                'success' => true,
                'message' => __('Candidate disqualified and moved to rejected pool.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reopen rejected application.
     */
    public function reopen(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-applications.reopen'), 403);

        try {
            $application = $this->service->reopenApplication($id);

            return response()->json([
                'success' => true,
                'message' => __('Candidate application reopened successfully.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update evaluation rating (1-5 stars).
     */
    public function updateRating(Request $request, int $id): JsonResponse
    {
        abort_if(! (auth()->user()->can('hr.job-applications.edit') || auth()->user()->can('hr.interviews.evaluate')), 403);

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        try {
            $application = $this->service->updateRating($id, (int)$request->rating);

            return response()->json([
                'success' => true,
                'rating'  => $application->rating,
                'message' => __('Evaluation rating updated.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Download Candidate CV / Resume.
     */
    public function downloadResume(int $id)
    {
        abort_if(! auth()->user()->can('hr.job-applications.download-resume'), 403);

        $application = $this->repository->findById($id);
        if (!$application || !$application->candidate || !$application->candidate->resume_path) {
            return back()->with('error', __('Resume file not found.'));
        }

        $path = $application->candidate->resume_path;
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', __('File does not exist in storage.'));
        }

        return Storage::disk('public')->download($path, $application->candidate->full_name . '_Resume.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Delete application.
     */
    public function destroy(int $id)
    {
        abort_if(! auth()->user()->can('hr.job-applications.delete'), 403);

        try {
            $this->repository->delete($id);
            return redirect()
                ->route('hr.job-applications.index')
                ->with('success', __('Application deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
