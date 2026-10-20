@extends('layouts/contentNavbarLayout')

@section('title', __('Task Groups') . ' - ' . config('variables.templateName'))

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
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                <i class="icon-base bx bx-folder-open text-primary"></i>
                {{ __('Task Groups') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Organize and manage projects and functional task groups.') }}</p>
        </div>
        @can('tasks.task-groups.create')
        <div>
            <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTaskGroup">
                <i class="icon-base bx bx-plus font-18"></i>
                <span>{{ __('Add New Group') }}</span>
            </button>
        </div>
        @endcan
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="filter_department_id">{{ __('Filter by Department') }}</label>
                    <select class="form-select select2" id="filter_department_id" data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
                        <option value="">{{ __('All Departments') }}</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="filter_is_active">{{ __('Filter by Status') }}</label>
                    <select class="form-select select2" id="filter_is_active" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="1">{{ __('Active') }}</option>
                        <option value="0">{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                        <i class="icon-base bx bx-reset me-1"></i>
                        {{ __('Reset Filters') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-datatable">
            <table id="taskGroupsTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>{{ __('Group Name') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th class="text-center">{{ __('Remaining') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Offcanvas: Add Task Group -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
         tabindex="-1" id="offcanvasAddTaskGroup" style="width: 520px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-folder-plus text-primary"></i>
                <span>{{ __('Add New Task Group') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="{{ route('tasks.task-groups.store') }}" method="POST" id="addTaskGroupForm">
                @csrf
                @include('tasks::task_groups.partials.form', ['prefix' => 'add'])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAdd">
                        <i class="icon-base bx bx-check me-1"></i>
                        {{ __('Save Group') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Offcanvas: Edit Task Group -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
         tabindex="-1" id="offcanvasEditTaskGroup" style="width: 520px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Task Group') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editTaskGroupForm">
                @csrf
                @method('PUT')
                @include('tasks::task_groups.partials.form', ['prefix' => 'edit'])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
                        <i class="icon-base bx bx-check me-1"></i>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteTaskGroupForm" action="" method="POST" class="d-none">
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

            // Universal Select2 Initializer with Bootstrap 5 Theme (produces .select2-selection__rendered)
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

            // Ensure plugins are initialized
            initPageSelect2(document);
            initPageFlatpickr(document);

            // Re-initialize whenever an offcanvas or modal is shown
            $(document).on('shown.bs.offcanvas shown.bs.modal', function (e) {
                initPageSelect2(e.target);
                initPageFlatpickr(e.target);
            });

            // Initialize DataTable
            const table = $('#taskGroupsTable').DataTable({
                processing: true,
                serverSide: true,
                pagingType: 'simple_numbers',
                ajax: {
                    url: '{{ route('tasks.task-groups.data') }}',
                    data: function (d) {
                        d.department_id = $('#filter_department_id').val();
                        d.is_active = $('#filter_is_active').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'department_name', name: 'department_id' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'remaining_days', name: 'end_date', className: 'text-center', orderable: false, searchable: false },
                    { data: 'is_active', name: 'is_active', className: 'text-center', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });

            // Filters changed
            $('#filter_department_id, #filter_is_active').on('change', function () {
                table.ajax.reload();
            });

            // Reset filters
            $('#btnResetFilters').on('click', function () {
                $('#filter_department_id').val('').trigger('change');
                $('#filter_is_active').val('').trigger('change');
                table.ajax.reload();
            });

            // Edit Task Group Modal
            $(document).on('click', '.btn-edit-group', function () {
                const btn = $(this);
                const id = btn.data('id');

                $('#editTaskGroupForm').attr('action', '{{ url('tasks/task-groups') }}/' + id);
                $('#edit_name_ar').val(btn.data('name-ar'));
                $('#edit_name_en').val(btn.data('name-en'));
                $('#edit_description_ar').val(btn.data('desc-ar'));
                $('#edit_description_en').val(btn.data('desc-en'));
                $('#edit_department_id').val(btn.data('department-id') || '').trigger('change');
                setDateValue('#edit_start_date', btn.data('start-date'));
                setDateValue('#edit_end_date', btn.data('end-date'));
                $('#edit_is_active').prop('checked', btn.data('is-active') == '1');

                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasEditTaskGroup')).show();
            });

            // Reset Add form on open
            $('#offcanvasAddTaskGroup').on('show.bs.offcanvas', function () {
                $('#addTaskGroupForm')[0].reset();
                $('#add_department_id').val('').trigger('change');
                setDateValue('#add_start_date', '');
                setDateValue('#add_end_date', '');
            });

            // Toggle Status Switch
            $(document).on('change', '.btn-toggle-status', function () {
                const checkbox = $(this);
                const id = checkbox.data('id');

                $.ajax({
                    url: '{{ url('tasks/task-groups') }}/' + id + '/toggle-status',
                    type: 'POST',
                    success: function (res) {
                        // Status updated successfully
                    },
                    error: function () {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        alert('{{ __('Failed to update status.') }}');
                    }
                });
            });

            // Delete Task Group Confirmation
            $(document).on('click', '.btn-delete-group', function () {
                const id = $(this).data('id');
                const name = $(this).data('name') || '';

                if (confirm('{{ __('Are you sure you want to delete this group?') }}\n' + name)) {
                    $('#deleteTaskGroupForm').attr('action', '{{ url('tasks/task-groups') }}/' + id).submit();
                }
            });
        });
    </script>
@endsection
