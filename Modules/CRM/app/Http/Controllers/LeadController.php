<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CRM\Models\Lead;
use Modules\CRM\Services\LeadService;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function index(): View
    {
        return view('crm::leads.index');
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->leadService->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->editColumn('email', fn (Lead $lead) => $lead->email ?: '-')
            ->editColumn('civil_id', fn (Lead $lead) => $lead->civil_id ?: '-')
            ->editColumn('job_title', fn (Lead $lead) => $lead->job_title ?: '-')
            ->editColumn('created_at', fn (Lead $lead) => $lead->created_at?->format('Y-m-d') ?? '-')
            ->addColumn('actions', function (Lead $lead) {
                $user = auth()->user();
                $canEdit = $user?->can('crm.leads.edit');
                $canDelete = $user?->can('crm.leads.delete');

                if (! $canEdit && ! $canDelete) {
                    return '<span class="text-muted">-</span>';
                }

                $buttons = '<div class="d-inline-flex gap-1">';
                if ($canEdit) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-lead"
                        data-id="' . $lead->id . '"
                        data-name="' . e($lead->name) . '"
                        data-phone="' . e($lead->phone) . '"
                        data-email="' . e($lead->email ?? '') . '"
                        data-civil-id="' . e($lead->civil_id ?? '') . '"
                        data-job-title="' . e($lead->job_title ?? '') . '"
                        data-address="' . e($lead->address ?? '') . '"
                        data-notes="' . e($lead->notes ?? '') . '"
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button>';
                }
                if ($canDelete) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-lead"
                        data-id="' . $lead->id . '" data-name="' . e($lead->name) . '" title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }
                $buttons .= '</div>';

                return $buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->leadService->createLead($this->validatedData($request));

        return redirect()->route('crm.leads.index')->with('success', __('Lead created successfully.'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $lead = $this->leadService->getLeadById($id);

        if (!$lead) {
            return redirect()->route('crm.leads.index')->with('error', __('Lead not found.'));
        }

        $this->leadService->updateLead($lead, $this->validatedData($request));

        return redirect()->route('crm.leads.index')->with('success', __('Lead updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $lead = $this->leadService->getLeadById($id);

            if (!$lead) {
                return redirect()->route('crm.leads.index')->with('error', __('Lead not found.'));
            }

            $this->leadService->deleteLead($lead);

            return redirect()->route('crm.leads.index')->with('success', __('Lead deleted successfully.'));
        } catch (Exception $exception) {
            return redirect()->route('crm.leads.index')
                ->with('error', __('An error occurred while deleting the lead: ') . $exception->getMessage());
        }
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:50'],
            'email'     => ['nullable', 'email', 'max:255'],
            'civil_id'  => ['nullable', 'string', 'max:100'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'address'   => ['nullable', 'string'],
            'notes'     => ['nullable', 'string'],
        ]);
    }
}
