@extends('layouts/contentNavbarLayout')

@section('title', __('Accounting System Guide') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .docs-nav-card {
        position: sticky;
        top: 5.5rem;
        z-index: 10;
    }
    .guide-tabs .nav-link {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        text-align: start !important;
        padding: 0.65rem 0.85rem !important;
        border-radius: 0.5rem;
        color: var(--bs-heading-color);
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        width: 100% !important;
    }
    .guide-tabs .nav-link:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--bs-primary);
    }
    .guide-tabs .nav-link.active {
        background-color: var(--bs-primary) !important;
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(var(--bs-primary-rgb), 0.35);
    }
    .guide-tabs .nav-link .guide-tab-icon {
        width: 2.15rem;
        height: 2.15rem;
        min-width: 2.15rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        background-color: rgba(var(--bs-primary-rgb), 0.08);
        margin-inline-end: 0.75rem;
        flex-shrink: 0;
        transition: all 0.2s ease-in-out;
    }
    .guide-tabs .nav-link .guide-tab-icon i {
        font-size: 1.15rem;
        transition: color 0.2s ease-in-out;
    }
    .guide-tabs .nav-link.active .guide-tab-icon {
        background-color: rgba(255, 255, 255, 0.2);
    }
    .guide-tabs .nav-link.active .guide-tab-icon i {
        color: #fff !important;
    }
    .guide-tabs .nav-link .guide-tab-title {
        flex-grow: 1;
        text-align: start;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .guide-tabs .nav-link .guide-tab-chevron {
        font-size: 1.1rem;
        opacity: 0.4;
        transition: all 0.2s ease-in-out;
        margin-inline-start: auto;
        flex-shrink: 0;
    }
    .guide-tabs .nav-link:hover .guide-tab-chevron,
    .guide-tabs .nav-link.active .guide-tab-chevron {
        opacity: 1;
        color: #fff;
        transform: translateX(-3px);
    }
    .workflow-step {
        position: relative;
        padding-inline-start: 2rem;
        margin-bottom: 1.25rem;
    }
    .workflow-step::before {
        content: "";
        position: absolute;
        inset-inline-start: 0.5rem;
        top: 0.25rem;
        bottom: -1rem;
        width: 2px;
        background-color: var(--bs-border-color);
    }
    .workflow-step:last-child::before {
        display: none;
    }
    .step-number {
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        width: 1.35rem;
        height: 1.35rem;
        border-radius: 50%;
        background-color: var(--bs-primary);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ifrs-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
        font-weight: 600;
    }
    .tab-footer-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 1.5rem;
        margin-top: 1.5rem;
        border-top: 1px dashed var(--bs-border-color);
    }
</style>
@endsection

@section('content')
<!-- Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-book-open text-primary font-24"></i>
            <span>{{ __('System Guide & Comprehensive Accounting Workflow') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('System Guide') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('accounting.dashboard') }}" class="btn btn-label-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to Dashboard') }}</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- 1. Sticky Navigation Tabs (Side Menu) -->
    <div class="col-12 col-lg-3">
        <div class="card shadow-sm border-0 docs-nav-card">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-list-ul text-primary font-20"></i>
                    <span>{{ __('System Screens Index') }}</span>
                </h6>
            </div>
            <div class="card-body p-2">
                <ul class="nav nav-pills flex-column guide-tabs gap-1" id="guideTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active"
                                id="tab-workflow-btn" data-bs-toggle="pill" data-bs-target="#pane-workflow" role="tab"
                                aria-controls="pane-workflow" aria-selected="true">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-refresh text-primary"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('1. Accounting Workflow') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-coa-btn" data-bs-toggle="pill" data-bs-target="#pane-coa" role="tab"
                                aria-controls="pane-coa" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-git-repo-forked text-success"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('2. Chart of Accounts Guide') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-journals-btn" data-bs-toggle="pill" data-bs-target="#pane-journals" role="tab"
                                aria-controls="pane-journals" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-book text-warning"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('3. Journals') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-entries-btn" data-bs-toggle="pill" data-bs-target="#pane-entries" role="tab"
                                aria-controls="pane-entries" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-receipt text-info"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('4. Journal Entries & Posting') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-treasury-btn" data-bs-toggle="pill" data-bs-target="#pane-treasury" role="tab"
                                aria-controls="pane-treasury" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-wallet text-secondary"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('5. Treasury & Bank Accounts') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-reconciliations-btn" data-bs-toggle="pill" data-bs-target="#pane-reconciliations" role="tab"
                                aria-controls="pane-reconciliations" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-check-shield text-danger"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('6. Bank Reconciliations') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-matching-btn" data-bs-toggle="pill" data-bs-target="#pane-matching" role="tab"
                                aria-controls="pane-matching" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-link text-primary"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('7. Payment Matching') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-reports-btn" data-bs-toggle="pill" data-bs-target="#pane-reports" role="tab"
                                aria-controls="pane-reports" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-bar-chart-alt-2 text-success"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('8. Financial Reports') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-lockdates-btn" data-bs-toggle="pill" data-bs-target="#pane-lockdates" role="tab"
                                aria-controls="pane-lockdates" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-lock-alt text-warning"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('9. Fiscal Years & Lock Dates') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-opening-btn" data-bs-toggle="pill" data-bs-target="#pane-opening" role="tab"
                                aria-controls="pane-opening" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-key text-info"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('10. Opening Balances') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link"
                                id="tab-audit-btn" data-bs-toggle="pill" data-bs-target="#pane-audit" role="tab"
                                aria-controls="pane-audit" aria-selected="false">
                            <span class="guide-tab-icon">
                                <i class="icon-base bx bx-history text-primary"></i>
                            </span>
                            <span class="guide-tab-title">{{ __('11. Audit Trail & Control') }}</span>
                            <i class="icon-base bx bx-chevron-left guide-tab-chevron"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 2. Documentation Tab Panes (Active section only displayed) -->
    <div class="col-12 col-lg-9">
        <div class="tab-content p-0 shadow-none border-0" id="guideTabContent">

            <!-- 1. Workflow Life Cycle -->
            <div class="tab-pane fade show active" id="pane-workflow" role="tabpanel" aria-labelledby="tab-workflow-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-refresh font-18 text-primary"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('1. Approved Accounting Lifecycle (Accounting Lifecycle)') }}</h5>
                        </div>
                        <span class="ifrs-badge">{{ __('International Financial Reporting Standard (IFRS)') }}</span>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <p class="text-muted mb-4">
                            يعمل النظام المحاسبي المتكامل وفق مبدأ القيد المزدوج (Double-Entry Bookkeeping)، حيث تتسلسل العمليات المحاسبية عبر دورة مغلقة تضمن سلامة الأرصدة وتوافق القوائم المالية:
                        </p>

                        <div class="workflow-step">
                            <div class="step-number">1</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Initial Setup & Structure Definition') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Define independent accounting entities, chart of accounts, specialized journals, and fiscal years.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">2</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Record Transactions & Journal Entries') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Create manual or automated journal entries in Draft status for verification, requiring exact balance between debit and credit.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">3</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Approval & Direct Posting (Posting)') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Post the entry to make it approved and non-editable, updating trial balance and general ledger in real time.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">4</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Matching, Reconciliation & Control (Reconciliation & Control)') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Reconcile bank statements with ledger books and match customer/vendor payments against outstanding invoices.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">5</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Extract Financial Statements & Year-End Closing (Financial Statements & Year-End)') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Generate trial balance, income statement, balance sheet, and lock fiscal periods to prevent retroactive modifications.') }}</p>
                        </div>

                        <div class="tab-footer-nav">
                            <div></div>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-coa-btn">
                                <span>{{ __('Next: Chart of Accounts') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Chart of Accounts -->
            <div class="tab-pane fade" id="pane-coa" role="tabpanel" aria-labelledby="tab-coa-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-git-repo-forked font-18 text-success"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('2. Chart of Accounts Guide (Chart of Accounts)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.accounts.index') }}" class="btn btn-sm btn-outline-success">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('Screen Purpose:') }}</h6>
                        <p class="text-muted mb-3">
                            العمود الفقري للنظام المالي؛ تحدد كافة الحسابات المالية مقسمة هرمياً (شجرياً) وفق التصنيف المحاسبي القياسي: الأصول (1)، الخصوم (2)، حقوق الملكية (3)، الإيرادات (4)، والمصروفات (5).
                        </p>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 bg-light-subtle h-100">
                                    <h6 class="fw-bold mb-1"><i class="bx bx-folder me-1 text-primary"></i>{{ __('Header Account (Summary):') }}</h6>
                                    <p class="small text-muted mb-0">{{ __('A parent account that aggregates child account balances and does not allow direct posting.') }}</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 bg-light-subtle h-100">
                                    <h6 class="fw-bold mb-1"><i class="bx bx-file me-1 text-success"></i>{{ __('Detail Account (Operational):') }}</h6>
                                    <p class="small text-muted mb-0">{{ __('A ledger account that allows posting and recording journal entries, with a specified normal balance (debit or credit).') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-primary d-flex align-items-center mb-0" role="alert">
                            <i class="bx bx-info-circle me-2 font-20"></i>
                            <div class="small">
                                <strong>{{ __('Advanced View Feature:') }}</strong> {{ __('The system supports two views: Interactive Hierarchical Tree, or fast Flat DataTable with search and filters.') }}
                            </div>
                        </div>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-workflow-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Workflow') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-journals-btn">
                                <span>{{ __('Next: Journals') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Journals -->
            <div class="tab-pane fade" id="pane-journals" role="tabpanel" aria-labelledby="tab-journals-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-book font-18 text-warning"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('3. Journals (Journals)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.journals.index') }}" class="btn btn-sm btn-outline-warning">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('Screen Purpose:') }}</h6>
                        <p class="text-muted mb-3">
                            تخصيص دفاتر محاسبية مستقلة لتنظيم وتصنيف الحركات حسب طبيعتها وفق أفضل المعايير المحاسبية المعتمدة (يومية مبيعات، مشتريات، بنك، نقدية، وعمليات عامة).
                        </p>
                        <ul class="text-muted small mb-3">
                            <li><strong>{{ __('Sales Journal (Sales):') }}</strong> {{ __('To record sales invoices and credit notes with their dedicated sequences.') }}</li>
                            <li><strong>{{ __('Purchase Journal (Purchase):') }}</strong> {{ __('To record vendor bills and purchase costs.') }}</li>
                            <li><strong>{{ __('Bank & Cash Journal (Bank & Cash):') }}</strong> {{ __('Directly linked to bank or cash accounts for immediate settlements.') }}</li>
                            <li><strong>{{ __('General / Miscellaneous Journal (General / Miscellaneous):') }}</strong> {{ __('For adjustment entries, payroll, depreciation, and closing entries.') }}</li>
                        </ul>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-coa-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Chart of Accounts') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-entries-btn">
                                <span>{{ __('Next: Journal Entries') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Journal Entries -->
            <div class="tab-pane fade" id="pane-entries" role="tabpanel" aria-labelledby="tab-entries-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-receipt font-18 text-info"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('4. Journal Entries & Posting (Journal Entries & Posting)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-sm btn-outline-info">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('Journal Entry Lifecycle:') }}</h6>
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                            <span class="badge bg-label-secondary px-3 py-2 font-12">{{ __('1. Draft') }}</span>
                            <i class="bx bx-left-arrow-alt text-muted"></i>
                            <span class="badge bg-label-success px-3 py-2 font-12">{{ __('2. Posted') }}</span>
                            <i class="bx bx-left-arrow-alt text-muted"></i>
                            <span class="badge bg-label-danger px-3 py-2 font-12">{{ __('3. Reversed') }}</span>
                        </div>
                        <ul class="text-muted small mb-3">
                            <li><strong>{{ __('Balance Validation:') }}</strong> {{ __('The system strictly prevents saving or posting any mathematically unbalanced entry (Total Debit must equal Total Credit).') }}</li>
                            <li><strong>{{ __('Strict Protection After Posting:') }}</strong> {{ __('Once posted, modifying or deleting the entry is prevented to preserve the audit trail. To correct errors, create an automated reversal entry with one click.') }}</li>
                            <li><strong>{{ __('Cost Center Allocation:') }}</strong> {{ __('Ability to link each journal line to an independent cost center to accurately track operating expenses and revenues.') }}</li>
                        </ul>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-journals-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Journals') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-treasury-btn">
                                <span>{{ __('Next: Treasury & Banks') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Treasury & Cash Accounts -->
            <div class="tab-pane fade" id="pane-treasury" role="tabpanel" aria-labelledby="tab-treasury-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-secondary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-wallet font-18 text-secondary"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('5. Treasury & Cash Accounts (Treasury & Cash Accounts)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.cash-accounts.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('Screen Purpose:') }}</h6>
                        <p class="text-muted mb-2">
                            إدارة دفاتر وأرصدة الصناديق النقدية (Cash Registers) والحسابات البنكية (Bank Accounts) مع كامل بياناتها البنكية (IBAN, Swift, Account #) وربطها التلقائي بالأستاذ العام ودفتر اليومية.
                        </p>
                        <p class="text-muted small mb-0">
                            - توريث العملة تلقائياً وثابتاً من الكيان المحاسبي.<br>
                            - متابعة الأرصدة الحية والدفترية لحظياً دون أي تأخير.
                        </p>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-entries-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Journal Entries') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-reconciliations-btn">
                                <span>{{ __('Next: Bank Reconciliations') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Bank Reconciliations -->
            <div class="tab-pane fade" id="pane-reconciliations" role="tabpanel" aria-labelledby="tab-reconciliations-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-check-shield font-18 text-danger"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('6. Bank Reconciliations Workspace (Bank Reconciliations Workspace)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.bank-reconciliations.index') }}" class="btn btn-sm btn-outline-danger">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('How does the Bank Reconciliation workspace work?') }}</h6>
                        <div class="workflow-step">
                            <div class="step-number">1</div>
                            <p class="text-muted small mb-0">{{ __('Enter ending statement balance and the statement date received from the bank.') }}</p>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">2</div>
                            <p class="text-muted small mb-0">{{ __('The system automatically fetches all unreconciled book transactions for the bank account.') }}</p>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">3</div>
                            <p class="text-muted small mb-0">{{ __('The accountant checks off cleared transactions, updating the bottom balance bar in real time.') }}</p>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">4</div>
                            <p class="text-muted small mb-0">{{ __('When difference reaches 0.00, approval button is enabled and the official bank reconciliation report is generated.') }}</p>
                        </div>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-treasury-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Treasury & Banks') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-matching-btn">
                                <span>{{ __('Next: Payment Matching') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Payment Matching -->
            <div class="tab-pane fade" id="pane-matching" role="tabpanel" aria-labelledby="tab-matching-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-link font-18 text-primary"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('7. Payment Matching & Allocations (Payment Matching & Allocations)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.payment-matchings.index') }}" class="btn btn-sm btn-outline-primary">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('Screen Purpose:') }}</h6>
                        <p class="text-muted mb-3">
                            ربط وتخصيص سندات القبض والمدفوعات بالفواتير المفتوحة (الذمم المدينة والدائنة)، مثل ربط دفعة سداد من عميل بفاتورة بيع محددة لمعرفة المتبقي بدقة.
                        </p>
                        <div class="alert alert-secondary d-flex align-items-center mb-0" role="alert">
                            <i class="bx bx-check-double me-2 font-20 text-success"></i>
                            <div class="small">
                                <strong>{{ __('Smart Matching Mechanism:') }}</strong> {{ __('Select a transaction from debit table and credit table; the system suggests allocated amount and allows partial or full matching, with one-click unlink.') }}
                            </div>
                        </div>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-reconciliations-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Bank Reconciliations') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-reports-btn">
                                <span>{{ __('Next: Financial Reports') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. Financial Reports -->
            <div class="tab-pane fade" id="pane-reports" role="tabpanel" aria-labelledby="tab-reports-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-bar-chart-alt-2 font-18 text-success"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('8. Financial Reports & Final Statements (Financial Reports)') }}</h5>
                        </div>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="fw-bold text-heading mb-1">{{ __('Trial Balance') }}</h6>
                                    <p class="text-muted small mb-2">{{ __('Key supervisory report showing total debit and credit movements and ending balances to ensure ledger balance.') }}</p>
                                    <a href="{{ route('accounting.reports.trial-balance') }}" class="btn btn-sm btn-outline-primary">{{ __('Preview Report') }}</a>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="fw-bold text-heading mb-1">{{ __('General Ledger') }}</h6>
                                    <p class="text-muted small mb-2">{{ __('Detailed historical log of all journal transactions per account with continuous running balance.') }}</p>
                                    <a href="{{ route('accounting.reports.general-ledger') }}" class="btn btn-sm btn-outline-primary">{{ __('Preview Report') }}</a>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="fw-bold text-heading mb-1">{{ __('Income Statement / Profit & Loss') }}</h6>
                                    <p class="text-muted small mb-2">{{ __('Compares realized revenues with operating expenses to determine net profit or loss for the period.') }}</p>
                                    <a href="{{ route('accounting.reports.income-statement') }}" class="btn btn-sm btn-outline-primary">{{ __('Preview Report') }}</a>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="fw-bold text-heading mb-1">{{ __('Balance Sheet') }}</h6>
                                    <p class="text-muted small mb-2">{{ __('Financial position statement at a specific date (Assets = Liabilities + Equity).') }}</p>
                                    <a href="{{ route('accounting.reports.balance-sheet') }}" class="btn btn-sm btn-outline-primary">{{ __('Preview Report') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-matching-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Payment Matching') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-lockdates-btn">
                                <span>{{ __('Next: Lock Dates') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 9. Fiscal Years & Lock Dates -->
            <div class="tab-pane fade" id="pane-lockdates" role="tabpanel" aria-labelledby="tab-lockdates-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-lock-alt font-18 text-warning"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('9. Fiscal Years & Lock Dates (Fiscal Years & Lock Dates)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.lock-dates.index') }}" class="btn btn-sm btn-outline-warning">{{ __('Open Screen') }}</a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading">{{ __('Period Protection & Closing Levels:') }}</h6>
                        <ul class="text-muted small mb-0">
                            <li><strong>{{ __('Journal Lock:') }}</strong> {{ __('Prevents recording or posting regular entries prior to a given date for non-authorized accountants.') }}</li>
                            <li><strong>{{ __('Tax Lock:') }}</strong> {{ __('Prevents modifying taxable invoices or entries after tax return filing.') }}</li>
                            <li><strong>{{ __('Hard Lock:') }}</strong> {{ __('Full freeze preventing any user from adding or altering entries prior to lock date.') }}</li>
                        </ul>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-reports-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Financial Reports') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-opening-btn">
                                <span>{{ __('Next: Opening Balances') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 10. Opening Balances -->
            <div class="tab-pane fade" id="pane-opening" role="tabpanel" aria-labelledby="tab-opening-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-key font-18 text-info"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('10. Opening Balances Management') }}</h5>
                        </div>
                        <a href="{{ route('accounting.opening-balances.index') }}" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1">
                            <i class="bx bx-link-external font-16"></i>
                            <span>{{ __('Open Screen') }}</span>
                        </a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading mb-2">{{ __('Screen Purpose & Importance:') }}</h6>
                        <p class="text-muted mb-4">
                            تُعد شاشة **الأرصدة الافتتاحية** نقطة الانطلاق الأساسية لتشغيل الدورة المحاسبية للكيان، سواء كان ذلك عند **بدء استخدام النظام لأول مرة** ونقل الأرصدة التاريخية من نظام قديم أو دفاتر ورقية، أو عند **تدوير أرصدة الإقفال للعام السابق** إلى سنة مالية جديدة.
                        </p>

                        <!-- Workflow steps -->
                        <h6 class="fw-bold text-heading mb-3">{{ __('Workflow & 5-Step Entry Process:') }}</h6>
                        
                        <div class="workflow-step">
                            <div class="step-number">1</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Select Entity & Fiscal Year') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Select target accounting entity and fiscal year; system automatically fetches all eligible operational accounts.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">2</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Review Accounts by Standard Classification') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Accounts are displayed grouped by nature (Assets, Liabilities, Equity) for streamlined review.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">3</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Enter Debit and Credit Amounts Accurately') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Enter initial balance in debit or credit according to actual starting balance.') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">4</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Monitor Sticky Balance Bar') }}</h6>
                            <p class="text-muted small mb-0">{{ __('A floating sticky bar displays real-time total debits, total credits, and difference (must equal 0.00).') }}</p>
                        </div>

                        <div class="workflow-step">
                            <div class="step-number">5</div>
                            <h6 class="fw-bold text-heading mb-1">{{ __('Approve, Post & Generate Opening Entry') }}</h6>
                            <p class="text-muted small mb-0">{{ __('When balanced, Save & Post generates and posts official opening journal entry immediately.') }}</p>
                        </div>

                        <!-- Highlights and Control Boxes -->
                        <div class="row g-3 my-2">
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 bg-light-subtle h-100">
                                    <h6 class="fw-bold mb-2 text-heading d-flex align-items-center gap-2">
                                        <i class="bx bx-check-shield text-success font-20"></i>
                                        <span>{{ __('Accounting Controls & Auditing:') }}</span>
                                    </h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li class="mb-1"><strong>{{ __('Prevent Unbalanced Posting:') }}</strong> {{ __('The system strictly forbids posting if any difference exists, even by one cent.') }}</li>
                                        <li class="mb-1"><strong>{{ __('Screen Lock After Posting:') }}</strong> {{ __('Fields become read-only to prevent accidental modification after approval.') }}</li>
                                        <li><strong>{{ __('Generate Official Entry:') }}</strong> {{ __('The entry is bound to Opening Journal and cannot be deleted via standard actions.') }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 bg-light-subtle h-100">
                                    <h6 class="fw-bold mb-2 text-heading d-flex align-items-center gap-2">
                                        <i class="bx bx-revision text-warning font-20"></i>
                                        <span>{{ __('Re-Open for Correction Feature:') }}</span>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        {{ __('In case of error after posting, Re-Open unposts entry temporarily for safe corrections.') }}
                                    </p>
                                    <span class="badge bg-label-warning font-11">{{ __('Requires permission: accounting.opening-balances.reopen') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- IFRS Rules alert -->
                        <div class="alert alert-primary d-flex align-items-start mt-3 mb-0" role="alert">
                            <i class="bx bx-info-circle me-2 mt-1 font-20"></i>
                            <div class="small">
                                <strong>{{ __('Important IFRS Accounting Rules:') }}</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>{{ __('Opening balances are restricted to balance sheet accounts (Assets, Liabilities, Equity).') }}</li>
                                    <li>{{ __('Revenue and expense accounts have no opening balances as they close annually into Retained Earnings.') }}</li>
                                    <li>{{ __('Print Statement button exports official document for external auditors.') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-lockdates-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Lock Dates') }}</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-audit-btn">
                                <span>{{ __('Next: Audit Trail') }}</span>
                                <i class="bx bx-left-arrow-alt font-18"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 11. Accounting Audit Trail -->
            <div class="tab-pane fade" id="pane-audit" role="tabpanel" aria-labelledby="tab-audit-btn">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-history font-18 text-primary"></i>
                            </span>
                            <h5 class="fw-bold mb-0 text-heading">{{ __('11. Accounting Audit Trail (Accounting Audit Trail)') }}</h5>
                        </div>
                        <a href="{{ route('accounting.audit-logs.index') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                            <i class="bx bx-link-external font-16"></i>
                            <span>{{ __('Open Screen') }}</span>
                        </a>
                    </div>
                    <div class="card-body pt-3 pb-3">
                        <h6 class="fw-bold text-heading mb-2">{{ __('Purpose & Regulatory Compliance (Audit & Compliance):') }}</h6>
                        <p class="text-muted mb-4">
                            تُعد شاشة **سجل النشاط والرقابة المالية** ركيزة الحوكمة والشفافية في النظام المالي، حيث تضمن توثيق وتسجيل كافة الأحداث والتغييرات التي تطرأ على البيانات المحاسبية لحظياً، وتمنع أي تلاعب أو تغيير غير معتمد، مع الاحتفاظ بالأثر الكامل لكل معاملة.
                        </p>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 bg-light-subtle h-100">
                                    <h6 class="fw-bold text-heading mb-2 d-flex align-items-center gap-2">
                                        <i class="bx bx-check-double text-success font-20"></i>
                                        <span>{{ __('What does the system automatically log?') }}</span>
                                    </h6>
                                    <ul class="text-muted small mb-0 ps-3">
                                        <li class="mb-1"><strong>{{ __('Posting & Reversing Entries:') }}</strong> {{ __('Logs who posted or reversed entries with exact timestamps.') }}</li>
                                        <li class="mb-1"><strong>{{ __('Editing Accounts & Treasuries:') }}</strong> {{ __('Logs modifications to account names, codes, or financial types.') }}</li>
                                        <li class="mb-1"><strong>{{ __('Reconciliation & Matching Sessions:') }}</strong> {{ __('Logs reconciliation approval, closing, and payment unlinking.') }}</li>
                                        <li><strong>{{ __('Closing & Exceptions:') }}</strong> {{ __('Logs period locking and special posting exceptions.') }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 bg-light-subtle h-100">
                                    <h6 class="fw-bold text-heading mb-2 d-flex align-items-center gap-2">
                                        <i class="bx bx-git-compare text-primary font-20"></i>
                                        <span>{{ __('Field Diffs Feature:') }}</span>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        {{ __('Clicking View Details displays an interactive modal comparing Old Value (red) and New Value (green).') }}
                                    </p>
                                    <span class="badge bg-label-primary font-11">{{ __('Precise Line & Value Comparison') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-footer-nav">
                            <button type="button" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-opening-btn">
                                <i class="bx bx-right-arrow-alt font-18"></i>
                                <span>{{ __('Previous: Opening Balances') }}</span>
                            </button>
                            <button type="button" class="btn btn-label-primary btn-sm d-flex align-items-center gap-1 switch-guide-tab" data-target="#tab-workflow-btn">
                                <i class="bx bx-rotate-left font-18"></i>
                                <span>{{ __('Back to Start: Workflow') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabsContainer = document.getElementById('guideTabs');
    if (!tabsContainer) return;

    // Handle hash on page load (e.g. #pane-reconciliations or #reconciliations)
    if (window.location.hash) {
        let hash = window.location.hash;
        if (!hash.startsWith('#pane-')) {
            hash = '#pane-' + hash.replace('#', '').replace('section-', '');
        }
        const targetBtn = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (targetBtn && typeof bootstrap !== 'undefined') {
            const tab = new bootstrap.Tab(targetBtn);
            tab.show();
        }
    }

    // Update URL hash when a tab is clicked
    const tabButtons = tabsContainer.querySelectorAll('button[data-bs-toggle="pill"]');
    tabButtons.forEach(function (button) {
        button.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId && history.replaceState) {
                history.replaceState(null, null, targetId.replace('#pane-', '#'));
            }
            // Scroll to top of content on smaller screens
            if (window.innerWidth < 992) {
                const contentEl = document.getElementById('guideTabContent');
                if (contentEl) {
                    contentEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Handle footer Next/Previous buttons
    document.addEventListener('click', function (e) {
        const switchBtn = e.target.closest('.switch-guide-tab');
        if (switchBtn) {
            const targetSelector = switchBtn.getAttribute('data-target');
            const targetBtn = document.querySelector(targetSelector);
            if (targetBtn && typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(targetBtn);
                tab.show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });
});
</script>
@endsection
