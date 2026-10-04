@extends('layouts/contentNavbarLayout')

@section('title', __('Task Monitoring') . ' - ' . config('variables.templateName'))

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- 1. Header Section -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 monitoring-header">
        <div>
            <h4 class="monitoring-title">{{ __('Task Monitoring') }}</h4>
            <span class="monitoring-subtitle">{{ __('Comprehensive overview of all tasks and operations in the organization') }}</span>
        </div>
        <div class="text-md-start text-end">
            <h4 class="monitoring-title">{{ __('Good morning') }} {{ auth()->user()->name ?? 'أ. أحمد' }}</h4>
            <span class="monitoring-subtitle">{{ __('Here is an overview of today\'s workflow in the organization') }}</span>
        </div>
    </div>

    <!-- 2. Top 6 KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Open Tasks -->
        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
            <div class="kpi-card shadow-xs">
                <div>
                    <div class="kpi-val text-dark">{{ $openTasks }}</div>
                    <div class="kpi-label">{{ __('Total Open Tasks') }}</div>
                </div>
                <div class="kpi-icon-square" style="background-color: #f1f5f9; color: #475569;">
                    <i class="icon-base bx bx-task"></i>
                </div>
            </div>
        </div>

        <!-- 2. Awaiting Approval -->
        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
            <div class="kpi-card shadow-xs">
                <div>
                    <div class="kpi-val" style="color: #7c3aed;">{{ $pendingApproval }}</div>
                    <div class="kpi-label">{{ __('Awaiting Approval') }}</div>
                </div>
                <div class="kpi-icon-circle" style="background-color: #7c3aed; color: #ffffff;">
                    <i class="icon-base bx bx-group"></i>
                </div>
            </div>
        </div>

        <!-- 3. Critical Tasks -->
        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
            <div class="kpi-card shadow-xs">
                <div>
                    <div class="kpi-val" style="color: #ef4444;">{{ $criticalTasks }}</div>
                    <div class="kpi-label">{{ __('Critical Tasks') }}</div>
                    <div class="kpi-sub text-danger">+2 {{ __('from yesterday') }}</div>
                </div>
                <div class="kpi-icon-circle" style="background-color: #ef4444; color: #ffffff;">
                    <i class="icon-base bx bx-error"></i>
                </div>
            </div>
        </div>

        <!-- 4. Overdue Tasks -->
        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
            <div class="kpi-card shadow-xs">
                <div>
                    <div class="kpi-val" style="color: #f59e0b;">{{ $overdueTasks }}</div>
                    <div class="kpi-label">{{ __('Overdue Tasks') }}</div>
                    <div class="kpi-sub text-warning">+5% {{ __('from yesterday') }}</div>
                </div>
                <div class="kpi-icon-circle" style="background-color: #f59e0b; color: #ffffff;">
                    <i class="icon-base bx bx-time-five"></i>
                </div>
            </div>
        </div>

        <!-- 5. In Progress Tasks -->
        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
            <div class="kpi-card shadow-xs">
                <div>
                    <div class="kpi-val" style="color: #0284c7;">{{ $inProgressTasks }}</div>
                    <div class="kpi-label">{{ __('In Progress Tasks') }}</div>
                </div>
                <div class="kpi-icon-circle" style="background-color: #0284c7; color: #ffffff;">
                    <i class="icon-base bx bx-time"></i>
                </div>
            </div>
        </div>

        <!-- 6. Completed Today -->
        <div class="col-12 col-sm-6 col-md-4 col-xl-2">
            <div class="kpi-card shadow-xs">
                <div>
                    <div class="kpi-val" style="color: #10b981;">{{ $completedToday }}</div>
                    <div class="kpi-label">{{ __('Completed Today') }}</div>
                    <div class="kpi-sub text-success">+12% {{ __('from yesterday') }}</div>
                </div>
                <div class="kpi-icon-circle" style="background-color: #10b981; color: #ffffff;">
                    <i class="icon-base bx bx-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="filter-bar-card shadow-sm mb-3">
        <div class="row g-2 align-items-center">
            <!-- Add New Task Button -->
            <div class="col-12 col-lg-auto">
                <button type="button" class="btn btn-create-task-main w-100" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTask">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Task') }}</span>
                </button>
            </div>

            <!-- Branch Filter -->
            <div class="col-6 col-md-3 col-lg">
                <select class="form-select filter-select select2" id="filter_branch_id" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Filter -->
            <div class="col-6 col-md-3 col-lg">
                <select class="form-select filter-select select2" id="filter_department_id" data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
                    <option value="">{{ __('All Departments') }}</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="col-6 col-md-3 col-lg">
                <select class="form-select filter-select select2" id="filter_priority" data-placeholder="{{ __('All Priorities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Priorities') }}</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-6 col-md-3 col-lg">
                <select class="form-select filter-select select2" id="filter_task_status" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="col-6 col-md-4 col-lg">
                <input type="text" class="form-control filter-input" id="filter_date_range" placeholder="{{ __('From - To') }}">
            </div>

            <!-- Search Input -->
            <div class="col-12 col-md-4 col-lg">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="icon-base bx bx-search"></i></span>
                    <input type="text" class="form-control filter-input border-start-0 ps-0" id="filter_custom_search" placeholder="{{ __('Search in tasks...') }}">
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Standard Sneat Tasks Data Table Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-datatable table-responsive">
            <table id="monitoringTasksTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('Task Code') }}</th>
                        <th>{{ __('Task Title') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Assigned Employee') }}</th>
                        <th class="text-center">{{ __('Priority') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th>{{ __('Due Date') }}</th>
                        <th class="text-center">{{ __('Remaining Duration') }}</th>
                        <th class="text-center" style="width: 90px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded dynamically via DataTables Ajax -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. 4 Analytics / Charts Section -->
    <div class="row g-3 mb-4">
        <!-- Chart 1: Task Distribution by Status (Donut) -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="chart-card shadow-xs">
                <div class="chart-card-title">{{ __('Task Distribution by Status') }}</div>
                <div id="statusDistributionChart" class="d-flex justify-content-center" style="min-height: 250px;"></div>
            </div>
        </div>

        <!-- Chart 2: Tasks by Department (Column Bar) -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="chart-card shadow-xs">
                <div class="chart-card-title">{{ __('Tasks by Department') }}</div>
                <div id="tasksByDepartmentChart" style="min-height: 250px;"></div>
            </div>
        </div>

        <!-- Chart 3: Top Branches with Overdue Tasks (Horizontal Bar) -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="chart-card shadow-xs">
                <div class="chart-card-title">{{ __('Top 5 Branches with Overdue Tasks') }}</div>
                <div id="branchesOverdueChart" style="min-height: 250px;"></div>
            </div>
        </div>

        <!-- Chart 4: Task Completion Rate (Radial Gauge) -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="chart-card shadow-xs">
                <div class="chart-card-title">{{ __('Task Completion Rate') }}</div>
                <div id="completionRateChart" class="d-flex justify-content-center" style="min-height: 220px;"></div>
                <div class="text-center text-muted font-12 fw-semibold mt-n1">{{ __('Of total') }}</div>
            </div>
        </div>
    </div>

    <!-- 6. Additional Helper / Auxiliary Summary Tables (Stacked under each other) -->
    
    <!-- Table 1: Critical & Overdue Tasks -->
    <div class="card shadow-sm border-0 mb-4 helper-table-card">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-error-alt font-18 text-danger"></i>
                </div>
                <h5 class="card-title fw-bold mb-0 font-15 text-heading">{{ __('Critical & Overdue Tasks') }}</h5>
            </div>
            <span class="badge bg-danger rounded-pill font-12 px-3 py-1">{{ count($criticalAndOverdueTasks) }} {{ __('Tasks') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-clean table-hover mb-0 w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Task Title') }}</th>
                            <th>{{ __('Assigned Employee') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th class="text-center">{{ __('Priority') }}</th>
                            <th class="text-center">{{ __('Remaining') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($criticalAndOverdueTasks as $cTask)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('tasks.tasks.show', $cTask->id) }}" class="fw-semibold text-heading text-primary">
                                        {{ $cTask->title }}
                                    </a>
                                </td>
                                <td>
                                    @if ($cTask->employee)
                                        <span class="fw-medium">{{ $cTask->employee->name }}</span>
                                    @else
                                        <span class="text-muted">{{ __('Unassigned') }}</span>
                                    @endif
                                </td>
                                <td>{{ $cTask->employee?->department?->name ?? $cTask->taskGroup?->department?->name ?? '-' }}</td>
                                <td>{{ $cTask->completion_date?->format('Y-m-d') ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($cTask->priority)
                                        <span class="badge {{ $cTask->priority->badgeClass() }} font-11">{{ $cTask->priority->label() }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{!! $cTask->remaining_days_badge !!}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('tasks.tasks.show', $cTask->id) }}" class="btn btn-icon btn-sm btn-outline-info" title="{{ __('View Details') }}">
                                            <i class="icon-base bx bx-show"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-quick-status" data-id="{{ $cTask->id }}" data-status="{{ $cTask->task_status?->value ?? 'pending' }}" title="{{ __('Update Status') }}">
                                            <i class="icon-base bx bx-check-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="icon-base bx bx-check-double font-28 text-success d-block mb-1"></i>
                                    {{ __('No critical or overdue tasks currently.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Table 2: Departments Progress Summary -->
    <div class="card shadow-sm border-0 mb-4 helper-table-card">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-buildings font-18 text-primary"></i>
                </div>
                <h5 class="card-title fw-bold mb-0 font-15 text-heading">{{ __('Departments Summary') }}</h5>
            </div>
            <span class="badge bg-label-primary rounded-pill font-12 px-3 py-1">{{ count($departmentSummaries) }} {{ __('Departments') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-clean table-hover mb-0 w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Department') }}</th>
                            <th class="text-center">{{ __('Total Tasks') }}</th>
                            <th class="text-center">{{ __('Completed') }}</th>
                            <th class="text-center">{{ __('In Progress') }}</th>
                            <th class="text-center">{{ __('Overdue') }}</th>
                            <th style="min-width: 200px;">{{ __('Completion Rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departmentSummaries as $dSum)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-heading">{{ $dSum['name'] }}</td>
                                <td class="text-center"><span class="badge bg-label-secondary font-12">{{ $dSum['total'] }}</span></td>
                                <td class="text-center"><span class="badge bg-label-success font-12">{{ $dSum['completed'] }}</span></td>
                                <td class="text-center"><span class="badge bg-label-primary font-12">{{ $dSum['in_progress'] }}</span></td>
                                <td class="text-center"><span class="badge bg-label-danger font-12">{{ $dSum['overdue'] }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress w-100" style="height: 8px; border-radius: 4px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $dSum['rate'] }}%" aria-valuenow="{{ $dSum['rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="font-12 fw-semibold text-heading">{{ $dSum['rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">{{ __('No departments data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Table 3: Employees Workload & Performance -->
    <div class="card shadow-sm border-0 mb-4 helper-table-card">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-check font-18 text-info"></i>
                </div>
                <h5 class="card-title fw-bold mb-0 font-15 text-heading">{{ __('Employees Workload') }}</h5>
            </div>
            <span class="badge bg-label-info rounded-pill font-12 px-3 py-1">{{ count($employeeSummaries) }} {{ __('Employees') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-clean table-hover mb-0 w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th class="text-center">{{ __('Assigned Tasks') }}</th>
                            <th class="text-center">{{ __('Completed') }}</th>
                            <th class="text-center">{{ __('Overdue') }}</th>
                            <th style="min-width: 180px;">{{ __('Performance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employeeSummaries as $eSum)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-heading d-flex align-items-center gap-2">
                                    <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="icon-base bx bx-user font-14"></i>
                                    </div>
                                    <span>{{ $eSum['name'] }}</span>
                                </td>
                                <td>{{ $eSum['department'] }}</td>
                                <td>{{ $eSum['branch'] }}</td>
                                <td class="text-center"><span class="badge bg-label-info font-12">{{ $eSum['total'] }}</span></td>
                                <td class="text-center"><span class="badge bg-label-success font-12">{{ $eSum['completed'] }}</span></td>
                                <td class="text-center"><span class="badge bg-label-danger font-12">{{ $eSum['overdue'] }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress w-100" style="height: 8px; border-radius: 4px;">
                                            <div class="progress-bar {{ $eSum['rate'] >= 70 ? 'bg-success' : ($eSum['rate'] >= 40 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" style="width: {{ $eSum['rate'] }}%" aria-valuenow="{{ $eSum['rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="font-12 fw-semibold text-heading">{{ $eSum['rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">{{ __('No employee data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Offcanvas: Add New Task -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
     tabindex="-1" id="offcanvasAddTask" aria-labelledby="offcanvasAddTaskLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddTaskLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary"></i>
            <span>{{ __('Add New Task') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form action="{{ route('tasks.tasks.store') }}" method="POST" id="addTaskForm" enctype="multipart/form-data">
            @csrf
            @include('tasks::tasks.partials.form', [
                'prefix' => 'add',
                'fixedGroupId' => null,
                'taskGroups' => $taskGroups,
                'employees' => $employees,
                'priorities' => $priorities,
                'statuses' => $statuses
            ])
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btnSubmitAddTask">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save Task') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Quick Update Status -->
<div class="modal fade" id="quickStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold font-15 mb-0">{{ __('Update Task Status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickStatusForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="quick_status">{{ __('Status') }}</label>
                        <select class="form-select select2" id="quick_status" name="status" required>
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}">{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" for="quick_comment">{{ __('Comment / Note') }}</label>
                        <textarea class="form-control" id="quick_comment" name="comment" rows="2" placeholder="{{ __('Optional reason...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitQuickStatus">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Quick Add Note -->
<div class="modal fade" id="quickNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold font-15 mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-comment-detail text-warning"></i>
                    <span>{{ __('Add Note to Task') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickNoteForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted font-13 mb-3" id="quickNoteTaskTitle"></p>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" for="quick_note_content">{{ __('Note Content') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quick_note_content" name="note" rows="3" required placeholder="{{ __('Write your note or instructions here...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitQuickNote">{{ __('Add Note') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteTaskForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const $ = window.jQuery || window.$;
        if (!$) return;

        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';

        // Universal Select2 Initializer
        function initPageSelect2(container) {
            const target = container ? $(container) : $(document);
            target.find('select.select2').each(function () {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    return;
                }
                const $parent = $el.closest('.offcanvas, .modal');
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $parent.length ? $parent : null,
                    placeholder: $el.data('placeholder') || '',
                    allowClear: $el.data('allow-clear') !== false && !$el.prop('required')
                });
            });
        }

        // Universal Flatpickr Initializer
        function initPageFlatpickr(container) {
            if (typeof flatpickr === 'undefined') return;
            const target = container ? $(container) : $(document);
            target.find('.js-flatpickr-date').each(function () {
                const input = this;
                if (!input._flatpickr) {
                    flatpickr(input, {
                        locale: isRtl && typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.ar ? flatpickr.l10ns.ar : 'default',
                        dateFormat: 'Y-m-d',
                        allowInput: true,
                        disableMobile: true,
                        position: isRtl ? 'auto right' : 'auto left'
                    });
                }
            });
        }

        // CSRF Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        initPageSelect2(document);
        initPageFlatpickr(document);

        $(document).on('shown.bs.offcanvas shown.bs.modal', function (e) {
            initPageSelect2(e.target);
            initPageFlatpickr(e.target);
        });

        // Range Date Picker for Filter
        let filterDateFrom = '';
        let filterDateTo = '';

        if (typeof flatpickr !== 'undefined') {
            flatpickr('#filter_date_range', {
                mode: 'range',
                locale: isRtl && typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.ar ? flatpickr.l10ns.ar : 'default',
                dateFormat: 'Y-m-d',
                onClose: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        filterDateFrom = instance.formatDate(selectedDates[0], 'Y-m-d');
                        filterDateTo = instance.formatDate(selectedDates[1], 'Y-m-d');
                    } else if (selectedDates.length === 0) {
                        filterDateFrom = '';
                        filterDateTo = '';
                    }
                    monitoringTable.ajax.reload();
                }
            });
        }

        // Initialize Standard Sneat DataTable
        const monitoringTable = $('#monitoringTasksTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            ajax: {
                url: '{{ route('tasks.monitoring.data') }}',
                data: function (d) {
                    d.branch_id = $('#filter_branch_id').val();
                    d.department_id = $('#filter_department_id').val();
                    d.priority = $('#filter_priority').val();
                    d.task_status = $('#filter_task_status').val();
                    d.date_from = filterDateFrom;
                    d.date_to = filterDateTo;
                    if ($('#filter_custom_search').val()) {
                        d.search = $('#filter_custom_search').val();
                    }
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'task_code', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'department', name: 'department_id' },
                { data: 'branch', name: 'branch_id' },
                { data: 'employee_name', name: 'employee_id' },
                { data: 'priority', name: 'priority', className: 'text-center' },
                { data: 'task_status', name: 'task_status', className: 'text-center' },
                { data: 'completion_date', name: 'completion_date' },
                { data: 'remaining_duration', name: 'completion_date', className: 'text-center', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        // Trigger filters
        $('#filter_branch_id, #filter_department_id, #filter_priority, #filter_task_status').on('change', function () {
            monitoringTable.ajax.reload();
        });

        $('#filter_custom_search').on('keyup', function () {
            monitoringTable.ajax.reload();
        });

        // Handle Add Task Submission
        $('#addTaskForm').on('submit', function (e) {
            e.preventDefault();
            const form = this;
            const $form = $(form);
            const $btn = $('#btnSubmitAddTask');
            const formData = new FormData(form);

            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');
            $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1" role="status"></span>');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $btn.prop('disabled', false).find('.spinner-border').remove();
                    const offcanvasEl = document.getElementById('offcanvasAddTask');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();
                    form.reset();
                    monitoringTable.ajax.reload(null, false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Success') }}',
                            text: response.message || '{{ __('Task created successfully.') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).find('.spinner-border').remove();
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            const input = $form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(messages[0]);
                        });
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Error') }}',
                                text: xhr.responseJSON?.message || '{{ __('An error occurred while creating the task.') }}'
                            });
                        }
                    }
                }
            });
        });

        // Handle Quick Status
        $(document).on('click', '.btn-quick-status', function () {
            const taskId = $(this).data('id');
            const currentStatus = $(this).data('status');
            $('#quickStatusForm').attr('action', '{{ url('tasks/tasks') }}/' + taskId + '/update-status');
            $('#quick_status').val(currentStatus).trigger('change');
            $('#quick_comment').val('');
            $('#quickStatusModal').modal('show');
        });

        $('#quickStatusForm').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $('#btnSubmitQuickStatus');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    $btn.prop('disabled', false);
                    $('#quickStatusModal').modal('hide');
                    monitoringTable.ajax.reload(null, false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Success') }}',
                            text: res.message || '{{ __('Status updated successfully.') }}',
                            timer: 1800,
                            showConfirmButton: false
                        });
                    }
                },
                error: function () {
                    $btn.prop('disabled', false);
                }
            });
        });

        // Handle Quick Note
        $(document).on('click', '.btn-quick-note', function () {
            const taskId = $(this).data('id');
            const taskTitle = $(this).data('title');
            $('#quickNoteForm').attr('action', '{{ url('tasks/tasks') }}/' + taskId + '/notes');
            $('#quickNoteTaskTitle').text('{{ __('Task') }}: ' + taskTitle);
            $('#quick_note_content').val('');
            $('#quickNoteModal').modal('show');
        });

        $('#quickNoteForm').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $('#btnSubmitQuickNote');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    $btn.prop('disabled', false);
                    $('#quickNoteModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Success') }}',
                            text: res.message || '{{ __('Note added successfully.') }}',
                            timer: 1800,
                            showConfirmButton: false
                        });
                    }
                },
                error: function () {
                    $btn.prop('disabled', false);
                }
            });
        });

        // Handle Delete
        $(document).on('click', '.btn-delete-task', function () {
            const taskId = $(this).data('id');
            const taskTitle = $(this).data('title');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
                    text: '{{ __('Do you want to delete task') }}: "' + taskTitle + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('Yes, delete') }}',
                    cancelButtonText: '{{ __('Cancel') }}',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $('#deleteTaskForm').attr('action', '{{ url('tasks/tasks') }}/' + taskId).submit();
                    }
                });
            } else {
                if (confirm('{{ __('Do you want to delete this task?') }}')) {
                    $('#deleteTaskForm').attr('action', '{{ url('tasks/tasks') }}/' + taskId).submit();
                }
            }
        });

        // ==========================================
        // APEXCHARTS INITIALIZATION
        // ==========================================
        if (typeof ApexCharts !== 'undefined') {
            
            // 1. Task Distribution Donut Chart
            const statusChartEl = document.querySelector('#statusDistributionChart');
            if (statusChartEl) {
                const statusOptions = {
                    series: @json($statusChartData['series']),
                    labels: @json($statusChartData['labels']),
                    chart: {
                        type: 'donut',
                        height: 230,
                        width: '100%',
                        fontFamily: 'inherit',
                        parentHeightOffset: 0,
                        toolbar: { show: false }
                    },
                    colors: ['#10b981', '#0ea5e9', '#f43f5e', '#a855f7', '#94a3b8'],
                    stroke: {
                        width: 2,
                        colors: ['#ffffff']
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '11px',
                        labels: {
                            colors: '#64748b'
                        },
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 12
                        },
                        itemMargin: {
                            horizontal: 5,
                            vertical: 2
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '11px',
                                        color: '#64748b',
                                        offsetY: -4
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 700,
                                        color: '#1e293b',
                                        offsetY: 2
                                    },
                                    total: {
                                        show: true,
                                        label: '{{ __('Total') }}',
                                        fontSize: '11px',
                                        color: '#64748b',
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
                new ApexCharts(statusChartEl, statusOptions).render();
            }

            // 2. Tasks by Department Column Chart
            const deptChartEl = document.querySelector('#tasksByDepartmentChart');
            if (deptChartEl) {
                const deptOptions = {
                    series: [{
                        name: '{{ __('Tasks') }}',
                        data: @json($deptSeries)
                    }],
                    chart: {
                        type: 'bar',
                        height: 240,
                        width: '100%',
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '40%',
                            distributed: true,
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    colors: ['#38bdf8', '#fbbf24', '#60a5fa', '#a78bfa', '#2dd4bf', '#818cf8', '#fb7185'],
                    dataLabels: {
                        enabled: true,
                        offsetY: -18,
                        style: {
                            fontSize: '10px',
                            colors: ['#475569'],
                            fontWeight: 600
                        }
                    },
                    legend: { show: false },
                    xaxis: {
                        categories: @json($deptLabels),
                        labels: {
                            style: {
                                fontSize: '10px',
                                colors: '#64748b'
                            },
                            rotate: -20
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        show: false
                    },
                    grid: {
                        show: false
                    }
                };
                new ApexCharts(deptChartEl, deptOptions).render();
            }

            // 3. Top Branches with Overdue Tasks Horizontal Bar Chart
            const branchesChartEl = document.querySelector('#branchesOverdueChart');
            if (branchesChartEl) {
                const branchesOptions = {
                    series: [{
                        name: '{{ __('Overdue Tasks') }}',
                        data: @json($branchSeries)
                    }],
                    chart: {
                        type: 'bar',
                        height: 240,
                        width: '100%',
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                            barHeight: '38%',
                            dataLabels: {
                                position: 'right'
                            }
                        }
                    },
                    colors: ['#f43f5e'],
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        offsetX: 5,
                        style: {
                            fontSize: '10px',
                            colors: ['#ffffff'],
                            fontWeight: 600
                        }
                    },
                    xaxis: {
                        categories: @json($branchLabels),
                        labels: { show: false },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '11px',
                                colors: '#64748b'
                            }
                        }
                    },
                    grid: {
                        show: false
                    }
                };
                new ApexCharts(branchesChartEl, branchesOptions).render();
            }

            // 4. Task Completion Rate RadialBar Gauge Chart
            const rateChartEl = document.querySelector('#completionRateChart');
            if (rateChartEl) {
                const rateOptions = {
                    series: [{{ $completionRate }}],
                    chart: {
                        type: 'radialBar',
                        height: 240,
                        width: '100%',
                        offsetY: -5,
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            hollow: {
                                size: '65%'
                            },
                            track: {
                                background: '#e2e8f0',
                                strokeWidth: '100%'
                            },
                            dataLabels: {
                                name: {
                                    show: false
                                },
                                value: {
                                    offsetY: 8,
                                    fontSize: '28px',
                                    fontWeight: 700,
                                    color: '#1e293b',
                                    formatter: function (val) {
                                        return val + '%';
                                    }
                                }
                            }
                        }
                    },
                    fill: {
                        colors: ['#10b981']
                    },
                    stroke: {
                        dashArray: 0
                    }
                };
                new ApexCharts(rateChartEl, rateOptions).render();
            }
        }
    });
</script>
@endsection
