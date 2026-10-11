@extends('layouts/contentNavbarLayout')

@section('title', $group->name . ' - ' . __('Task Group & Tasks'))

@section('page-style')
    <style>
        .select2-container--bootstrap-5 .select2-selection,
        .select2-container .select2-selection {
            min-height: 38px;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        .select2-container .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            color: var(--bs-body-color);
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--bs-primary) !important;
            box-shadow: none !important;
        }
    </style>
@endsection

@section('content')
    <!-- Header & Breadcrumbs -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('tasks.task-groups.index') }}">{{ __('Task Groups') }}</a></li>
                    <li class="breadcrumb-item active">{{ $group->name }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2 text-heading">
                <i class="icon-base bx bx-folder-open text-primary"></i>
                <span>{{ $group->name }}</span>
                @if ($group->is_active)
                    <span class="badge bg-label-success fs-tiny ms-2">{{ __('Active') }}</span>
                @else
                    <span class="badge bg-label-secondary fs-tiny ms-2">{{ __('Inactive') }}</span>
                @endif
                <div class="ms-1">{!! $group->remaining_days_badge !!}</div>
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tasks.task-groups.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i class="icon-base bx bx-arrow-back"></i>
                <span>{{ __('Back to Groups') }}</span>
            </a>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTask">
                <i class="icon-base bx bx-plus font-18"></i>
                <span>{{ __('Add Task to Group') }}</span>
            </button>
        </div>
    </div>

    <!-- Group Information Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-info-circle text-primary font-18"></i>
                <span>{{ __('Group Details & Information') }}</span>
            </h5>
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#groupDetailsCollapse" aria-expanded="false" aria-controls="groupDetailsCollapse">
                <i class="icon-base bx bx-chevron-down"></i>
                <span>{{ __('Toggle Details') }}</span>
            </button>
        </div>
        <div class="collapse" id="groupDetailsCollapse">
            <div class="card-body pt-3">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted d-block small mb-1">{{ __('Department') }}</span>
                        <span class="fw-semibold text-heading">{{ $group->department?->name ?: '-' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted d-block small mb-1">{{ __('Created By') }}</span>
                        <span class="fw-semibold text-heading">{{ $group->creator?->name ?: '-' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted d-block small mb-1">{{ __('Start Date') }}</span>
                        <span class="fw-semibold">{{ $group->start_date?->format('Y-m-d') ?: '-' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted d-block small mb-1">{{ __('End Date & Remaining') }}</span>
                        <div class="d-flex flex-column gap-1">
                            <span class="fw-semibold">{{ $group->end_date?->format('Y-m-d') ?: '-' }}</span>
                            {!! $group->remaining_days_badge !!}
                        </div>
                    </div>
                    @if ($group->translateOrNew('ar')->description || $group->translateOrNew('en')->description)
                        <div class="col-12 border-top pt-2 mt-2">
                            <span class="text-muted d-block small mb-1">{{ __('Description') }}</span>
                            <p class="mb-0 text-body small">
                                {{ $group->description ?: '-' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold" for="filter_employee_id">{{ __('Filter by Employee') }}</label>
                    <select class="form-select select2" id="filter_employee_id" data-placeholder="{{ __('All Employees') }}" data-allow-clear="true">
                        <option value="">{{ __('All Employees') }}</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold" for="filter_task_status">{{ __('Filter by Status') }}</label>
                    <select class="form-select select2" id="filter_task_status" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold" for="filter_priority">{{ __('Filter by Priority') }}</label>
                    <select class="form-select select2" id="filter_priority" data-placeholder="{{ __('All Priorities') }}" data-allow-clear="true">
                        <option value="">{{ __('All Priorities') }}</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                        <i class="icon-base bx bx-reset font-18 me-1"></i>
                        <span>{{ __('Reset Filters') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Data Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-datatable">
            <table id="tasksTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('Task Title') }}</th>
                        <th>{{ __('Assigned Employee') }}</th>
                        <th class="text-center">{{ __('Priority') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th>{{ __('Due Date') }}</th>
                        <th class="text-center">{{ __('Remaining') }}</th>
                        <th class="text-center">{{ __('Attachments') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Offcanvas: Add Task -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
         tabindex="-1" id="offcanvasAddTask" aria-labelledby="offcanvasAddTaskLabel" style="width: 520px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasAddTaskLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-plus-circle text-primary"></i>
                <span>{{ __('Add New Task to Group') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="{{ route('tasks.tasks.store') }}" method="POST" id="addTaskForm" enctype="multipart/form-data">
                @csrf
                @include('tasks::tasks.partials.form', [
                    'prefix' => 'add',
                    'fixedGroupId' => $group->id,
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

    <!-- Offcanvas: Edit Task -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
         tabindex="-1" id="offcanvasEditTask" aria-labelledby="offcanvasEditTaskLabel" style="width: 520px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasEditTaskLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Task') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editTaskForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('tasks::tasks.partials.form', [
                    'prefix' => 'edit',
                    'fixedGroupId' => $group->id,
                    'employees' => $employees,
                    'priorities' => $priorities,
                    'statuses' => $statuses
                ])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btnSubmitEditTask">
                        <i class="icon-base bx bx-check font-18"></i>
                        <span>{{ __('Save Changes') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form for Delete -->
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

            // Universal Select2 Initializer with Bootstrap 5 Theme
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
                        placeholder: $el.data('placeholder') || ($el.find('option:first').val() === '' ? $el.find('option:first').text() : ''),
                        allowClear: $el.data('allow-clear') !== false && !$el.prop('required')
                    });
                });
            }

            // Universal Flatpickr Initializer for .js-flatpickr-date
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

            // Helper to set Flatpickr value
            function setDateValue(selector, val) {
                const el = document.querySelector(selector);
                if (el) {
                    if (el._flatpickr) {
                        el._flatpickr.setDate(val || '', true);
                    } else {
                        el.value = val || '';
                    }
                }
            }

            // CSRF Setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initial Plugin Inits
            initPageSelect2(document);
            initPageFlatpickr(document);

            // Re-initialize plugins when offcanvas or modal is shown
            $(document).on('shown.bs.offcanvas shown.bs.modal', function (e) {
                initPageSelect2(e.target);
                initPageFlatpickr(e.target);
            });

            // Initialize Tasks DataTable for this specific group
            const table = $('#tasksTable').DataTable({
                processing: true,
                serverSide: true,
                pagingType: 'simple_numbers',
                ajax: {
                    url: '{{ route('tasks.tasks.data') }}',
                    data: function (d) {
                        d.task_group_id = '{{ $group->id }}';
                        d.employee_id = $('#filter_employee_id').val();
                        d.task_status = $('#filter_task_status').val();
                        d.priority = $('#filter_priority').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'employee_name', name: 'employee_id' },
                    { data: 'priority', name: 'priority', className: 'text-center' },
                    { data: 'task_status', name: 'task_status', className: 'text-center' },
                    { data: 'completion_date', name: 'completion_date' },
                    { data: 'remaining_days', name: 'completion_date', className: 'text-center', orderable: false, searchable: false },
                    { data: 'attachments_count', name: 'attachments_count', className: 'text-center', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });

            // Filter triggers
            $('#filter_employee_id, #filter_task_status, #filter_priority').on('change', function () {
                table.ajax.reload();
            });

            $('#btnResetFilters').on('click', function () {
                $('#filter_employee_id').val('').trigger('change');
                $('#filter_task_status').val('').trigger('change');
                $('#filter_priority').val('').trigger('change');
                table.ajax.reload();
            });

            // Handle Add Task Form Submit
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
                        if (response.success) {
                            const offcanvasEl = document.getElementById('offcanvasAddTask');
                            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                            if (offcanvasInstance) offcanvasInstance.hide();

                            form.reset();
                            $('#add_employee_id').val('').trigger('change');
                            $('#add_priority').val('medium').trigger('change');
                            $('#add_task_status').val('pending').trigger('change');
                            setDateValue('#add_start_date', '');
                            setDateValue('#add_completion_date', '');

                            table.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Success!') }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').remove();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, messages) {
                                const input = $form.find(`[name="${key}"], [name="${key}[]"]`);
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(messages[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Error!') }}',
                                text: xhr.responseJSON?.message || '{{ __('Something went wrong.') }}',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    }
                });
            });

            // Handle Edit Task Button Click
            $(document).on('click', '.btn-edit-task', function () {
                const id = $(this).data('id');
                const url = '{{ url('tasks/tasks') }}/' + id + '/edit';

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            const form = $('#editTaskForm');
                            form.attr('action', '{{ url('tasks/tasks') }}/' + id);

                            $('#edit_title_ar').val(data.title_ar);
                            $('#edit_title_en').val(data.title_en);
                            $('#edit_body_ar').val(data.body_ar);
                            $('#edit_body_en').val(data.body_en);
                            $('#edit_employee_id').val(data.employee_id).trigger('change');
                            $('#edit_priority').val(data.priority).trigger('change');
                            $('#edit_task_status').val(data.task_status).trigger('change');
                            setDateValue('#edit_start_date', data.start_date);
                            setDateValue('#edit_completion_date', data.completion_date);

                            // Manage Attachments in Edit Form
                            const $attachContainer = $('#existingAttachmentsContainer');
                            const $attachList = $('#existingAttachmentsList');
                            $attachList.empty();

                            if (data.attachments && data.attachments.length > 0) {
                                $attachContainer.show();
                                $.each(data.attachments, function (i, att) {
                                    const icon = att.is_image ? 'bx-image' : (att.is_pdf ? 'bxs-file-pdf' : 'bx-file');
                                    const html = `
                                        <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3" id="att-item-${att.id}">
                                            <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 320px;">
                                                <i class="icon-base bx ${icon} font-20 text-primary"></i>
                                                <a href="${att.url}" target="_blank" class="text-heading text-truncate small">${att.original_name}</a>
                                                <span class="text-muted small">(${att.file_size})</span>
                                            </div>
                                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-single-att" data-id="${att.id}">
                                                <i class="icon-base bx bx-trash font-16"></i>
                                            </button>
                                        </div>
                                    `;
                                    $attachList.append(html);
                                });
                            } else {
                                $attachContainer.hide();
                            }

                            const offcanvasEl = document.getElementById('offcanvasEditTask');
                            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                            offcanvas.show();
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error!') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to fetch task details.') }}',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                });
            });

            // Delete single attachment
            $(document).on('click', '.btn-delete-single-att', function () {
                const attId = $(this).data('id');
                const $item = $(`#att-item-${attId}`);

                Swal.fire({
                    title: '{{ __('Delete Attachment?') }}',
                    text: '{{ __('Are you sure you want to delete this attachment file?') }}',
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
                        $.ajax({
                            url: '{{ url('tasks/tasks/attachments') }}/' + attId,
                            method: 'DELETE',
                            success: function (res) {
                                if (res.success) {
                                    $item.slideUp(200, function () { $(this).remove(); });
                                    table.ajax.reload(null, false);
                                }
                            }
                        });
                    }
                });
            });

            // Update Task Form Submit
            $('#editTaskForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const $form = $(form);
                const $btn = $('#btnSubmitEditTask');
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
                        if (response.success) {
                            const offcanvasEl = document.getElementById('offcanvasEditTask');
                            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                            if (offcanvasInstance) offcanvasInstance.hide();

                            table.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Success!') }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').remove();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, messages) {
                                const input = $form.find(`[name="${key}"], [name="${key}[]"]`);
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(messages[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Error!') }}',
                                text: xhr.responseJSON?.message || '{{ __('Something went wrong.') }}',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    }
                });
            });

            // Delete Task Confirmation
            $(document).on('click', '.btn-delete-task', function () {
                const id = $(this).data('id');
                const title = $(this).data('title');

                Swal.fire({
                    title: '{{ __('Delete Task?') }}',
                    text: '{{ __('Are you sure you want to delete:') }} ' + title + '?',
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
                        $.ajax({
                            url: '{{ url('tasks/tasks') }}/' + id,
                            method: 'POST',
                            data: {
                                _method: 'DELETE'
                            },
                            success: function (response) {
                                if (response.success) {
                                    table.ajax.reload(null, false);
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('Deleted!') }}',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        customClass: { confirmButton: 'btn btn-primary' }
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('Error!') }}',
                                    text: xhr.responseJSON?.message || '{{ __('Failed to delete task.') }}',
                                    customClass: { confirmButton: 'btn btn-primary' }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
