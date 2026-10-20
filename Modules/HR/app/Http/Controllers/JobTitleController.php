<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Http\Requests\StoreJobTitleRequest;
use Modules\HR\Http\Requests\UpdateJobTitleRequest;
use Modules\HR\Models\JobTitle;
use Modules\HR\Services\JobTitleService;
use Yajra\DataTables\Facades\DataTables;

class JobTitleController extends Controller
{
    //======================
    public function __construct(
        protected JobTitleService $jobTitleService
    ) {}

    //======================
    /**
     * Display listing view for Job Titles in HR Settings.
     */
    public function index(): View
    {
        abort_if(! auth()->user()->can('hr.settings.job-titles.view'), 403);

        $formData = $this->jobTitleService->getFormData();

        return view('hr::settings.job_titles.index', [
            'departments' => $formData['departments'],
            'statuses'    => $formData['statuses'],
        ]);
    }

    //======================
    /**
     * Server-side DataTables endpoint for Job Titles.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.job-titles.view'), 403);

        $query = $this->jobTitleService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(JobTitle $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(JobTitle $item) => $item->translate('en')?->name ?? '-')
            ->addColumn('department_name', fn(JobTitle $item) => $item->department?->name ?? '<span class="text-muted">' . __('General / Not Assigned') . '</span>')
            ->addColumn('department_id', fn(JobTitle $item) => $item->department_id)
            ->editColumn('code', fn(JobTitle $item) => $item->code ?? '-')
            ->editColumn('status', function (JobTitle $item) {
                $statusEnum = $item->status;
                $badgeClass = $statusEnum ? $statusEnum->badgeColor() : 'bg-label-secondary';
                $label = $statusEnum ? $statusEnum->label() : ($item->status ?? '-');

                return '<span class="badge ' . $badgeClass . '">' . $label . '</span>';
            })
            ->addColumn('status_raw', fn(JobTitle $item) => is_object($item->status) ? $item->status->value : $item->status)
            ->addColumn('description_ar', fn(JobTitle $item) => $item->translate('ar')?->description ?? '')
            ->addColumn('description_en', fn(JobTitle $item) => $item->translate('en')?->description ?? '')
            ->editColumn('created_at', fn(JobTitle $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (JobTitle $item) {
                $user = auth()->user();
                $statusVal = is_object($item->status) ? $item->status->value : $item->status;

                $editBtn = '';
                if ($user->can('hr.settings.job-titles.edit')) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect btn-edit-job-title" 
                        data-id="' . $item->id . '" 
                        data-name-ar="' . e($item->translate('ar')?->name ?? '') . '" 
                        data-name-en="' . e($item->translate('en')?->name ?? '') . '" 
                        data-description-ar="' . e($item->translate('ar')?->description ?? '') . '" 
                        data-description-en="' . e($item->translate('en')?->description ?? '') . '" 
                        data-code="' . e($item->code ?? '') . '" 
                        data-department-id="' . ($item->department_id ?? '') . '" 
                        data-status="' . $statusVal . '"
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit text-primary font-18"></i>
                    </button>';
                }

                $deleteBtn = '';
                if ($user->can('hr.settings.job-titles.delete')) {
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect btn-delete-job-title" 
                        data-id="' . $item->id . '" 
                        data-name="' . e($item->name ?? '') . '" 
                        title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash text-danger font-18"></i>
                    </button>';
                }

                $buttons = $editBtn . $deleteBtn;
                return $buttons ? '<div class="d-inline-block text-nowrap">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['department_name', 'status', 'actions'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->make(true);
    }

    //======================
    /**
     * Store a newly created job title.
     */
    public function store(StoreJobTitleRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.job-titles.create'), 403);

        try {
            $this->jobTitleService->createJobTitle($request->validated(), auth()->id());

            return redirect()->route('hr.settings.job-titles.index')
                ->with('success', __('Job Title created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while creating the job title: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified job title.
     */
    public function update(UpdateJobTitleRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.job-titles.edit'), 403);

        try {
            $jobTitle = $this->jobTitleService->getJobTitleById($id);
            if (!$jobTitle) {
                return redirect()->route('hr.settings.job-titles.index')
                    ->with('error', __('Job title not found.'));
            }

            $this->jobTitleService->updateJobTitle($jobTitle, $request->validated());

            return redirect()->route('hr.settings.job-titles.index')
                ->with('success', __('Job Title updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while updating the job title: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified job title.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.job-titles.delete'), 403);

        try {
            $jobTitle = $this->jobTitleService->getJobTitleById($id);
            if (!$jobTitle) {
                return redirect()->route('hr.settings.job-titles.index')
                    ->with('error', __('Job title not found.'));
            }

            $this->jobTitleService->deleteJobTitle($jobTitle);

            return redirect()->route('hr.settings.job-titles.index')
                ->with('success', __('Job Title deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('hr.settings.job-titles.index')
                ->with('error', __('An error occurred while deleting the job title: ') . $e->getMessage());
        }
    }
}
