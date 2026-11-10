@extends('layouts/contentNavbarLayout')

@section('title', __('Clearance Details') . ' - ' . $clearance->clearance_code)

@section('content')
<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('hr.exit-clearances.index') }}" class="btn btn-icon btn-outline-secondary btn-sm rounded-circle">
                <i class="bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 text-heading">
                {{ __('Exit Clearance') }}: <span class="text-primary">{{ $clearance->clearance_code }}</span>
            </h4>
            <span class="badge {{ $clearance->status?->badgeClass() ?? 'bg-label-primary' }} ms-2">
                {{ $clearance->status?->label() ?? $clearance->status }}
            </span>
        </div>
        <p class="text-muted mb-0 small">{{ __('Review department sign-offs, asset returns, and execute final settlement.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('hr.exit-clearances.print', $clearance->id) }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-printer font-16"></i>
            <span>{{ __('Print Clearance Form') }}</span>
        </a>
        @if($clearance->status != \Modules\HR\Enums\ExitClearanceStatus::CLEARED)
            <button type="button" class="btn btn-success d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#finalizeModal">
                <i class="bx bx-check-double font-16"></i>
                <span>{{ __('Finalize & Clear Employee') }}</span>
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Employee & Financial Overview -->
    <div class="col-12 col-lg-4">
        <!-- Employee Profile Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="avatar avatar-lg">
                        <span class="avatar-initial rounded-circle bg-label-primary font-20">
                            {{ mb_substr($clearance->employee?->name ?? 'E', 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-heading">{{ $clearance->employee?->name ?? '-' }}</h5>
                        <span class="badge bg-label-secondary">{{ $clearance->employee?->employee_code ?? '' }}</span>
                    </div>
                </div>

                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Department') }}:</span>
                        <span class="fw-semibold text-heading">{{ $clearance->department?->name ?? '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Job Title') }}:</span>
                        <span class="fw-semibold text-heading">{{ $clearance->jobTitle?->name ?? '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Direct Manager') }}:</span>
                        <span class="fw-semibold text-heading">{{ $clearance->employee?->manager?->name ?? __('Not Assigned') }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Hire Date') }}:</span>
                        <span class="fw-semibold">{{ $clearance->employee?->hire_date?->format('Y-m-d') ?? '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Last Working Day') }}:</span>
                        <span class="fw-bold text-danger">{{ $clearance->last_working_day?->format('Y-m-d') }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Leaving Reason') }}:</span>
                        <span class="badge bg-label-info">{{ __(ucfirst($clearance->leaving_reason)) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bx bx-wallet text-success font-18"></i>
                    <span>{{ __('Settlements & Financial Summary') }}</span>
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-3 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('End of Service Pay') }}:</span>
                        <span class="fw-bold text-success">{{ number_format($clearance->end_of_service_amount, 2) }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Remaining Leave Balance') }}:</span>
                        <span class="fw-semibold">{{ $clearance->leave_balance_days }} {{ __('Days') }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Outstanding Loans & Advances') }}:</span>
                        <span class="fw-bold text-danger">-{{ number_format($clearance->outstanding_loans_amount, 2) }}</span>
                    </li>
                    <li class="d-flex justify-content-between border-top pt-2">
                        <span class="fw-bold text-heading">{{ __('Estimated Net Settlement') }}:</span>
                        <span class="fw-bold text-primary font-16">{{ number_format($clearance->net_settlement_amount, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Department Clearances & Checklists -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bx bx-check-shield text-primary font-18"></i>
                    <span>{{ __('Department Handover & Clearances') }}</span>
                </h6>
                <span class="badge bg-label-primary">
                    {{ $clearance->approvals->where('status', \Modules\HR\Enums\ClearanceApprovalStatus::CLEARED)->count() }} / {{ $clearance->approvals->count() }} {{ __('Cleared') }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($clearance->approvals as $approval)
                        <div class="list-group-item p-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2 mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold mb-0 text-heading">{{ $approval->department_name }}</h6>
                                        <span class="badge {{ $approval->status?->badgeClass() ?? 'bg-label-warning' }}">
                                            <i class="{{ $approval->status?->icon() ?? 'bx bx-time' }} me-1"></i>
                                            {{ $approval->status?->label() ?? $approval->status }}
                                        </span>
                                    </div>
                                    @if($approval->approved_at)
                                        <small class="text-muted d-block mt-1">
                                            {{ __('Signed by') }}: <span class="fw-semibold">{{ $approval->approver?->name ?? 'System' }}</span>
                                            ({{ $approval->approved_at->format('Y-m-d H:i') }})
                                        </small>
                                    @endif
                                </div>
                                <div>
                                    @if($clearance->status != \Modules\HR\Enums\ExitClearanceStatus::CLEARED)
                                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#signModal-{{ $approval->id }}">
                                            <i class="bx bx-edit font-14"></i>
                                            <span>{{ __('Update Sign-off') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Checklist Items -->
                            @if(!empty($approval->items_checklist))
                                <div class="bg-light rounded p-2 mb-2">
                                    <small class="fw-semibold text-muted d-block mb-1">{{ __('Required Verification Checklist') }}:</small>
                                    <ul class="mb-0 ps-3 small text-muted">
                                        @foreach($approval->items_checklist as $chk)
                                            <li>{{ $chk }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($approval->notes)
                                <div class="alert alert-secondary py-1 px-2 mb-0 small">
                                    <strong>{{ __('Notes') }}:</strong> {{ $approval->notes }}
                                </div>
                            @endif

                            @if($approval->dues_amount > 0)
                                <div class="alert alert-danger py-1 px-2 mt-2 mb-0 small">
                                    <i class="bx bx-error-circle me-1"></i>
                                    <strong>{{ __('Pending Dues') }}:</strong> {{ number_format($approval->dues_amount, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Modal for Sign-off -->
                        <div class="modal fade" id="signModal-{{ $approval->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('hr.exit-clearances.approvals.update', $approval->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">{{ __('Department Sign-off') }} - {{ $approval->department_name }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('Clearance Status') }}</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="cleared" {{ $approval->status->value == 'cleared' ? 'selected' : '' }}>{{ __('Cleared / No Dues') }}</option>
                                                    <option value="has_dues" {{ $approval->status->value == 'has_dues' ? 'selected' : '' }}>{{ __('Pending Dues / Assets Not Returned') }}</option>
                                                    <option value="rejected" {{ $approval->status->value == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('Pending Dues Amount (If any)') }}</label>
                                                <input type="number" step="0.01" name="dues_amount" class="form-control" value="{{ $approval->dues_amount }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">{{ __('Verification Notes & Asset Checklist') }}</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Confirm asset handover or specify missing items...') }}">{{ $approval->notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('Save Sign-off') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Finalize Modal -->
<div class="modal fade" id="finalizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('hr.exit-clearances.finalize', $clearance->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                        <i class="bx bx-check-circle font-22"></i>
                        <span>{{ __('Final Clearance Approval') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning small mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ __('This action will mark the clearance as Fully Cleared, update employee status to Resigned/Terminated, and auto-deactivate system login if enabled in HR policies.') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Final HR Sign-off Notes') }}</label>
                        <textarea name="final_notes" class="form-control" rows="3" placeholder="{{ __('Settlement check numbers, certificate issuance notes, etc.') }}"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="force_override" value="1" id="overrideCheck">
                        <label class="form-check-label small" for="overrideCheck">
                            {{ __('Force approve even if some department checklists have dues (Emergency Override)') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                        <i class="bx bx-check font-18"></i>
                        <span>{{ __('Confirm Final Clearance') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

