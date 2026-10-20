<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobOfferStatus;
use Modules\HR\Http\Requests\StoreJobOfferRequest;
use Modules\HR\Http\Requests\UpdateJobOfferRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\JobOffer;
use Modules\HR\Models\JobTitle;
use Modules\HR\Repositories\JobOfferRepository;
use Modules\HR\Services\JobOfferService;
use Yajra\DataTables\Facades\DataTables;

class JobOfferController extends Controller
{
    public function __construct(
        protected JobOfferRepository $repository,
        protected JobOfferService $service
    ) {}

    public function index(Request $request)
    {
        abort_if(! auth()->user()->can('hr.job-offers.view'), 403);

        $selectedOpeningId  = $request->get('job_opening_id');
        $selectedStatus     = $request->get('status');
        $selectedDepartment = $request->get('department_id');

        $kpis = $this->repository->getKpis();
        $jobOpenings = \Modules\HR\Models\JobOpening::with(['translations', 'jobTitle'])->latest()->get();
        $departments = Department::with('translations')->where('status', 'active')->get();
        $statuses = JobOfferStatus::cases();

        return view('hr::job_offers.index', compact(
            'kpis',
            'jobOpenings',
            'departments',
            'statuses',
            'selectedOpeningId',
            'selectedStatus',
            'selectedDepartment'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.view'), 403);

        $filters = $request->only(['status', 'department_id', 'job_opening_id']);
        $query = $this->repository->getDataTableQuery($filters);
        $settingCurrency = \Modules\Settings\Models\Setting::instance()->currency ?? 'KWD';

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('offer_code_info', function (JobOffer $item) {
                return '<div>
                            <a href="' . route('hr.job-offers.show', $item->id) . '" class="fw-bold text-primary font-14">
                                <i class="bx bx-file-blank me-1"></i>' . e($item->offer_code) . '
                            </a>
                            <small class="text-muted d-block"><i class="bx bx-calendar font-11 me-1"></i>' . $item->offer_date->format('Y-m-d') . '</small>
                        </div>';
            })
            ->addColumn('candidate_info', function (JobOffer $item) {
                $cand = $item->candidate;
                if (!$cand) return '<span class="text-muted">-</span>';

                $avatar = '<div class="avatar avatar-sm me-2 d-inline-block">
                                <span class="avatar-initial rounded-circle bg-label-primary font-11 fw-bold">' . e($cand->initials) . '</span>
                           </div>';

                return '<div class="d-flex align-items-center">
                            ' . $avatar . '
                            <div>
                                <span class="fw-semibold text-heading font-13 d-block">' . e($cand->full_name) . '</span>
                                <small class="text-muted"><i class="bx bx-phone font-11 me-1"></i>' . e($cand->phone) . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('opening_info', function (JobOffer $item) {
                $opening = $item->jobOpening;
                if (!$opening) return '<span class="text-muted">-</span>';
                $locale = app()->getLocale();
                $title = $opening->translate($locale)?->title ?? $opening->title;
                return '<div class="d-flex flex-column">
                            <span class="badge bg-label-primary rounded-pill px-2 py-0.5 font-11 fw-semibold w-fit mb-1">' . e($opening->opening_code) . '</span>
                            <span class="fw-semibold text-heading small">' . e($title) . '</span>
                        </div>';
            })
            ->addColumn('position_info', function (JobOffer $item) {
                $locale = app()->getLocale();
                $title = $item->jobTitle?->translate($locale)?->title ?? $item->jobTitle?->title ?? '-';
                $dept  = $item->department?->translate($locale)?->name ?? $item->department?->name ?? '-';

                return '<div>
                            <span class="fw-semibold text-heading font-13 d-block">' . e($title) . '</span>
                            <small class="text-muted"><i class="bx bx-building font-11 me-1"></i>' . e($dept) . '</small>
                        </div>';
            })
            ->addColumn('compensation_info', function (JobOffer $item) use ($settingCurrency) {
                $curr  = e($item->currency ?: $settingCurrency);
                $total = number_format($item->total_salary, 2);
                $basic = number_format($item->basic_salary, 2);

                return '<div>
                            <span class="fw-bold text-success font-13 d-block">' . $total . ' ' . $curr . '</span>
                            <small class="text-muted">' . __('Basic') . ': ' . $basic . ' ' . $curr . '</small>
                        </div>';
            })
            ->addColumn('status_badge', function (JobOffer $item) {
                return '<span class="badge ' . $item->status->badgeClass() . ' rounded-pill px-2.5 py-1 font-11">
                            <i class="' . $item->status->icon() . ' me-1 font-11"></i>' . $item->status->label() . '
                        </span>';
            })
            ->addColumn('expiry_info', function (JobOffer $item) {
                if (!$item->expiry_date) return '<span class="text-muted">-</span>';
                $isExpired = $item->expiry_date->isPast();
                $cls = $isExpired ? 'text-danger fw-bold' : 'text-muted';

                return '<span class="font-12 ' . $cls . '">' . $item->expiry_date->format('Y-m-d') . '</span>';
            })
            ->addColumn('actions', function (JobOffer $item) {
                $user = auth()->user();

                $showBtn = '';
                if ($user->can('hr.job-offers.view')) {
                    $showBtn = '<a href="' . route('hr.job-offers.show', $item->id) . '" class="btn btn-sm btn-icon btn-outline-secondary" title="' . __('View / Print Offer') . '">
                                    <i class="bx bx-show font-16"></i>
                                </a>';
                }

                $editBtn = '';
                if (in_array($item->status, [JobOfferStatus::DRAFT, JobOfferStatus::PENDING_APPROVAL]) && $user->can('hr.job-offers.edit')) {
                    $editBtn = '<a href="' . route('hr.job-offers.edit', $item->id) . '" class="btn btn-sm btn-icon btn-outline-primary" title="' . __('Edit') . '">
                                    <i class="bx bx-edit font-16"></i>
                                </a>';
                }

                $deleteBtn = '';
                if ($item->status === JobOfferStatus::DRAFT && $user->can('hr.job-offers.delete')) {
                    $deleteBtn = '<form action="' . route('hr.job-offers.destroy', $item->id) . '" method="POST" class="d-inline delete-offer-form">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger btn-delete-offer" title="' . __('Delete') . '">
                                        <i class="bx bx-trash font-16"></i>
                                    </button>
                                  </form>';
                }

                $buttons = $showBtn . $editBtn . $deleteBtn;
                return $buttons ? '<div class="d-flex align-items-center justify-content-center gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['offer_code_info', 'candidate_info', 'opening_info', 'position_info', 'compensation_info', 'status_badge', 'expiry_info', 'actions'])
            ->make(true);
    }

    public function create(Request $request)
    {
        abort_if(! auth()->user()->can('hr.job-offers.create'), 403);

        $applicationId = $request->get('application_id');
        $prefilledApp  = null;

        if ($applicationId) {
            $prefilledApp = JobApplication::with([
                'candidate',
                'jobOpening.jobTitle',
                'jobOpening.department',
            ])->find($applicationId);
        }

        $applications = JobApplication::with(['candidate', 'jobOpening.jobTitle'])
            ->whereIn('stage', ['interview', 'offer'])
            ->latest()
            ->get();

        $departments     = Department::with('translations')->where('status', 'active')->get();
        $jobTitles       = JobTitle::with('translations')->where('status', 'active')->get();
        $employmentTypes = EmploymentType::cases();
        $systemSetting   = \Modules\Settings\Models\Setting::instance();
        $systemCurrency  = $systemSetting->currency ?? 'KWD';
        $systemCurrencySymbol = $systemSetting->currency_symbol ?? 'د.ك';

        return view('hr::job_offers.create', compact(
            'prefilledApp',
            'applications',
            'departments',
            'jobTitles',
            'employmentTypes',
            'systemCurrency',
            'systemCurrencySymbol'
        ));
    }

    public function store(StoreJobOfferRequest $request)
    {
        abort_if(! auth()->user()->can('hr.job-offers.create'), 403);

        $application = JobApplication::with('jobOpening')->findOrFail($request->job_application_id);

        $data = $request->validated();
        $data['candidate_id']   = $application->candidate_id;
        $data['job_opening_id'] = $application->job_opening_id;

        $offer = $this->service->createOffer($data, auth()->id() ?? 1);

        return redirect()->route('hr.job-offers.show', $offer->id)
            ->with('success', __('Job Offer created successfully.'));
    }

    public function show(int $id)
    {
        abort_if(! auth()->user()->can('hr.job-offers.view'), 403);

        $offer = $this->repository->findWithRelations($id);
        if (!$offer) {
            abort(404, __('Job Offer not found.'));
        }

        return view('hr::job_offers.show', compact('offer'));
    }

    public function edit(int $id)
    {
        abort_if(! auth()->user()->can('hr.job-offers.edit'), 403);

        $offer = $this->repository->findWithRelations($id);
        if (!$offer) {
            abort(404, __('Job Offer not found.'));
        }

        $departments     = Department::with('translations')->where('status', 'active')->get();
        $jobTitles       = JobTitle::with('translations')->where('status', 'active')->get();
        $employmentTypes = EmploymentType::cases();
        $systemSetting   = \Modules\Settings\Models\Setting::instance();
        $systemCurrency  = $systemSetting->currency ?? 'KWD';
        $systemCurrencySymbol = $systemSetting->currency_symbol ?? 'د.ك';

        return view('hr::job_offers.edit', compact('offer', 'departments', 'jobTitles', 'employmentTypes', 'systemCurrency', 'systemCurrencySymbol'));
    }

    public function update(UpdateJobOfferRequest $request, int $id)
    {
        abort_if(! auth()->user()->can('hr.job-offers.edit'), 403);

        $offer = JobOffer::findOrFail($id);
        $this->service->updateOffer($offer, $request->validated());

        return redirect()->route('hr.job-offers.show', $offer->id)
            ->with('success', __('Job Offer updated successfully.'));
    }

    public function submitForApproval(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.submit'), 403);

        $offer = JobOffer::findOrFail($id);
        $this->service->submitForApproval($offer);

        return response()->json([
            'success' => true,
            'message' => __('Job Offer submitted for management approval.'),
        ]);
    }

    public function approve(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.approve'), 403);

        $offer = JobOffer::findOrFail($id);
        $this->service->approveOffer($offer, auth()->id() ?? 1);

        return response()->json([
            'success' => true,
            'message' => __('Job Offer approved successfully.'),
        ]);
    }

    public function send(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.send'), 403);

        $offer = JobOffer::findOrFail($id);
        $this->service->sendOffer($offer);

        return response()->json([
            'success' => true,
            'message' => __('Job Offer marked as sent to candidate.'),
        ]);
    }

    public function accept(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.accept'), 403);

        $offer = JobOffer::findOrFail($id);
        $this->service->acceptOffer($offer);

        return response()->json([
            'success' => true,
            'message' => __('Candidate accepted the offer! Onboarding journey initialized.'),
        ]);
    }

    public function decline(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.decline'), 403);

        $request->validate(['rejection_reason' => 'nullable|string|max:500']);
        $offer = JobOffer::findOrFail($id);
        $this->service->declineOffer($offer, $request->rejection_reason);

        return response()->json([
            'success' => true,
            'message' => __('Job Offer recorded as declined.'),
        ]);
    }

    public function cancel(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.job-offers.cancel'), 403);

        $offer = JobOffer::findOrFail($id);
        $this->service->cancelOffer($offer);

        return response()->json([
            'success' => true,
            'message' => __('Job Offer cancelled.'),
        ]);
    }

    public function destroy(int $id)
    {
        abort_if(! auth()->user()->can('hr.job-offers.delete'), 403);

        $offer = JobOffer::findOrFail($id);
        $offer->delete();

        return redirect()->route('hr.job-offers.index')
            ->with('success', __('Job Offer deleted successfully.'));
    }
}
