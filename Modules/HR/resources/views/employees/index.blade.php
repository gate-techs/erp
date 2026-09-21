@extends('layouts/contentNavbarLayout')

@section('title', __('Employees') . ' - ' . config('variables.templateName'))

@section('content')
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-id-card text-primary font-24"></i>
                <span>{{ __('Employees') }}</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Employees') }}</li>
                </ol>
            </nav>
        </div>

        <div>
            <a href="{{ route('hr.employees.create') }}"
                class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm">
                <i class="icon-base bx bx-plus font-18"></i>
                <span>{{ __('Add New Employee') }}</span>
            </a>
        </div>
    </div>



    <!-- Filters Card (نفس تصميم nursery-branches تماماً) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <!-- 1. Branch Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                    <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}"
                        data-allow-clear="true">
                        <option value="">{{ __('All Branches') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Department Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Department') }}</label>
                    <select id="filter_department_id" class="select2 form-select"
                        data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
                        <option value="">{{ __('All Departments') }}</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">
                                {{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Job Title Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Job Title') }}</label>
                    <select id="filter_job_title_id" class="select2 form-select"
                        data-placeholder="{{ __('All Job Titles') }}" data-allow-clear="true">
                        <option value="">{{ __('All Job Titles') }}</option>
                        @foreach ($jobTitles as $job)
                            <option value="{{ $job->id }}">
                                {{ $job->translate(app()->getLocale())?->name ?? $job->translate('ar')?->name ?? $job->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Status Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                    <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}"
                        data-allow-clear="true">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Employment Type Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Employment Type') }}</label>
                    <select id="filter_employment_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}"
                        data-allow-clear="true">
                        <option value="">{{ __('All Types') }}</option>
                        @foreach ($employmentTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Gender Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Gender') }}</label>
                    <select id="filter_gender" class="select2 form-select" data-placeholder="{{ __('All') }}"
                        data-allow-clear="true">
                        <option value="">{{ __('All') }}</option>
                        <option value="female">{{ __('Female') }}</option>
                        <option value="male">{{ __('Male') }}</option>
                    </select>
                </div>

                <!-- 7. Work Schedule Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Work Schedule') }}</label>
                    <select id="filter_work_schedule_id" class="select2 form-select"
                        data-placeholder="{{ __('All Schedules') }}" data-allow-clear="true">
                        <option value="">{{ __('All Schedules') }}</option>
                        @foreach ($workSchedules as $sched)
                            <option value="{{ $sched->id }}">
                                {{ $sched->translate(app()->getLocale())?->name ?? $sched->translate('ar')?->name ?? $sched->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 8. Shift Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Shift') }}</label>
                    <select id="filter_shift_id" class="select2 form-select" data-placeholder="{{ __('All Shifts') }}"
                        data-allow-clear="true">
                        <option value="">{{ __('All Shifts') }}</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->translate(app()->getLocale())?->name ?? $shift->translate('ar')?->name ?? $shift->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 9. Nationality Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Nationality') }}</label>
                    <select id="filter_nationality" class="select2 form-select"
                        data-placeholder="{{ __('All Nationalities') }}" data-allow-clear="true">
                        <option value="">{{ __('All Nationalities') }}</option>
                        @foreach ($nationalities as $nat)
                            <option value="{{ $nat->id }}">
                                {{ $nat->translate(app()->getLocale())?->name ?? $nat->translate('ar')?->name ?? $nat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 10. Marital Status Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Marital Status') }}</label>
                    <select id="filter_marital_status" class="select2 form-select"
                        data-placeholder="{{ __('All Marital Statuses') }}" data-allow-clear="true">
                        <option value="">{{ __('All Marital Statuses') }}</option>
                        @foreach ($maritalStatuses as $ms)
                            <option value="{{ $ms->id }}">
                                {{ $ms->translate(app()->getLocale())?->name ?? $ms->translate('ar')?->name ?? $ms->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 11. Hire Date From Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Hire Date From') }}</label>
                    <input type="date" id="filter_hire_date_from" class="form-control">
                </div>

                <!-- 12. Hire Date To Filter with Reset Button -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label fw-semibold text-heading small mb-0">{{ __('Hire Date To') }}</label>
                        <button type="button" id="btnResetFilters"
                            class="btn btn-link btn-xs text-muted p-0 text-decoration-none"
                            title="{{ __('Reset Filters') }}">
                            <i class="icon-base bx bx-reset me-1"></i>{{ __('Reset') }}
                        </button>
                    </div>
                    <input type="date" id="filter_hire_date_to" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <!-- Employees DataTable Card -->
    <div class="card shadow-sm border-0">
        <div class="card-datatable">
            <table id="employeesTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Position & Department') }}</th>
                        <th>{{ __('Work System') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th>{{ __('Hire Date') }}</th>
                        <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables Server-Side -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="avatar avatar-lg bg-label-danger mx-auto mb-3">
                        <i class="icon-base bx bx-trash font-28 text-danger"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ __('Delete Employee') }}</h5>
                    <p class="text-muted small mb-3">
                        {{ __('Are you sure you want to delete the employee record:') }}
                        <br>
                        <span id="deleteItemName" class="fw-bold text-heading"></span>
                    </p>
                    <form id="deleteEmployeeForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-sm btn-danger">{{ __('Yes, Delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dataUrl = "{{ route('hr.employees.data') }}";
            const baseUrl = "{{ url('hr/employees') }}";


            // 1. Initialize Server-Side DataTable
            const table = $('#employeesTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                ajax: {
                    url: dataUrl,
                    data: function (d) {
                        d.branch_id = $('#filter_branch_id').val();
                        d.department_id = $('#filter_department_id').val();
                        d.job_title_id = $('#filter_job_title_id').val();
                        d.status = $('#filter_status').val();
                        d.employment_type = $('#filter_employment_type').val();
                        d.gender = $('#filter_gender').val();
                        d.work_schedule_id = $('#filter_work_schedule_id').val();
                        d.shift_id = $('#filter_shift_id').val();
                        d.nationality = $('#filter_nationality').val();
                        d.marital_status = $('#filter_marital_status').val();
                        d.hire_date_from = $('#filter_hire_date_from').val();
                        d.hire_date_to = $('#filter_hire_date_to').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'employee_info',
                        name: 'name',
                        orderable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'job_info',
                        name: 'job_title_id',
                        orderable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'work_info',
                        name: 'work_schedule_id',
                        orderable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'contact_info',
                        name: 'phone',
                        orderable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'hire_date',
                        name: 'hire_date',
                        className: 'align-middle'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    }
                ],
                order: [[6, 'desc']],
                pagingType: 'simple_numbers',
                drawCallback: function () {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('#employeesTable [data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });

            // 2. Custom Filters Trigger Reload with debounce / reset guard
            let isResetting = false;

            $('#filter_branch_id, #filter_department_id, #filter_job_title_id, #filter_status, #filter_employment_type, #filter_gender, #filter_work_schedule_id, #filter_shift_id, #filter_nationality, #filter_marital_status, #filter_hire_date_from, #filter_hire_date_to').on('change', function () {
                if (isResetting) return;
                table.ajax.reload();
            });

            // 3. Reset Filters Handler (Single clean reload)
            $('#btnResetFilters').on('click', function () {
                isResetting = true;
                $('#filter_branch_id, #filter_department_id, #filter_job_title_id, #filter_status, #filter_employment_type, #filter_gender, #filter_work_schedule_id, #filter_shift_id, #filter_nationality, #filter_marital_status').val('').trigger('change');
                $('#filter_hire_date_from, #filter_hire_date_to').val('');
                isResetting = false;
                table.ajax.reload();
            });

            // 4. Delete Action
            const deleteModalEl = document.getElementById('deleteConfirmModal');
            const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
            const deleteForm = document.getElementById('deleteEmployeeForm');
            const deleteTargetName = document.getElementById('deleteItemName');

            $(document).on('click', '.btn-delete-employee', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');

                deleteForm.action = baseUrl + '/' + id;
                deleteTargetName.textContent = name || '';

                if (deleteModal) {
                    deleteModal.show();
                }
            });
        });
    </script>
@endsection