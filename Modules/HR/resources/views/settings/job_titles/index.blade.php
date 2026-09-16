@extends('layouts/contentNavbarLayout')

@section('title', __('Job Titles') . ' - ' . config('variables.templateName'))

@section('content')
    <!-- Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
            {{ __('HR Settings') }}
        </h4>
    </div>

    <!-- 2-Column Layout with Sub-Sidebar -->
    <div class="row g-4">
        <!-- Sub-Sidebar Column -->
        <div class="col-12 col-md-4 col-lg-3">
            @include('hr::settings.partials.sidebar')
        </div>

        <!-- Main Content Column -->
        <div class="col-12 col-md-8 col-lg-9">
            <!-- Action Bar: Title & Add Button -->
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                        <i class="icon-base bx bx-id-card text-primary"></i>
                        {{ __('Job Titles') }}
                    </h5>
                </div>

                <div>
                    <button type="button"
                        class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddJobTitle"
                        aria-controls="offcanvasAddJobTitle">
                        <i class="icon-base bx bx-plus font-18"></i>
                        <span>{{ __('Add New Job Title') }}</span>
                    </button>
                </div>
            </div>

            <!-- Job Titles Server-Side DataTable Card -->
            <div class="card shadow-sm border-0">
                <div class="card-datatable">
                    <table id="jobTitlesTable" class="table table-hover border-top w-100 text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>{{ __('Arabic Name') }}</th>
                                <th>{{ __('English Name') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via DataTables Server-Side -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. Offcanvas: Add New Job Title                                           -->
    <!-- ========================================================================= -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
        id="offcanvasAddJobTitle" aria-labelledby="offcanvasAddJobTitleLabel" style="width: 480px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasAddJobTitleLabel"
                class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-id-card text-primary"></i>
                <span>{{ __('Add New Job Title') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4">
            <form action="{{ route('hr.settings.job-titles.store') }}" method="POST" id="addJobTitleForm">
                @csrf

                <!-- Department Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Department') }}</label>
                    <select name="department_id" id="add_department_id" class="select2 form-select" data-placeholder="{{ __('Select Department') }}" data-allow-clear="true">
                        <option value="">{{ __('General / Not Assigned') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Job Title Name [Arabic] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Job Title Name [Arabic]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control"
                        placeholder="{{ __('e.g. مدير الموارد البشرية') }}" required dir="rtl">
                </div>

                <!-- Job Title Name [English] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Job Title Name [English]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" placeholder="{{ __('e.g. HR Manager') }}"
                        required dir="ltr">
                </div>

                <!-- Job Code / Symbol -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Job Code') }}</label>
                    <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. HR-MGR') }}">
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select" name="status" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ $status->value === 'active' ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description [Arabic] -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label>
                    <textarea name="description_ar" class="form-control" rows="2"
                        placeholder="{{ __('Optional description...') }}" dir="rtl"></textarea>
                </div>

                <!-- Description [English] -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('Description [English]') }}</label>
                    <textarea name="description_en" class="form-control" rows="2"
                        placeholder="{{ __('Optional description...') }}" dir="ltr"></textarea>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-check font-18"></i>
                        <span>{{ __('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. Offcanvas: Edit Job Title                                              -->
    <!-- ========================================================================= -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
        id="offcanvasEditJobTitle" aria-labelledby="offcanvasEditJobTitleLabel" style="width: 480px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasEditJobTitleLabel"
                class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Job Title') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editJobTitleForm">
                @csrf
                @method('PUT')

                <!-- Department Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Department') }}</label>
                    <select name="department_id" id="edit_department_id" class="select2 form-select" data-placeholder="{{ __('Select Department') }}" data-allow-clear="true">
                        <option value="">{{ __('General / Not Assigned') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Job Title Name [Arabic] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Job Title Name [Arabic]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required dir="rtl">
                </div>

                <!-- Job Title Name [English] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Job Title Name [English]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="edit_name_en" name="name_en" class="form-control" required dir="ltr">
                </div>

                <!-- Job Code / Symbol -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Job Code') }}</label>
                    <input type="text" id="edit_code" name="code" class="form-control">
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select" id="edit_status" name="status" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description [Arabic] -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label>
                    <textarea id="edit_description_ar" name="description_ar" class="form-control" rows="2"
                        dir="rtl"></textarea>
                </div>

                <!-- Description [English] -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('Description [English]') }}</label>
                    <textarea id="edit_description_en" name="description_en" class="form-control" rows="2"
                        dir="ltr"></textarea>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-check font-18"></i>
                        <span>{{ __('Save Changes') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. Hidden Form for Delete Action                                          -->
    <!-- ========================================================================= -->
    <form id="deleteJobTitleForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('page-script')
    <script>
        window.addEventListener('load', function () {
            const $ = window.jQuery || window.$;
            if (!$ || !$.fn.DataTable) {
                console.error('jQuery or DataTables not loaded');
                return;
            }

            const dataUrl = "{{ route('hr.settings.job-titles.data') }}";
            const baseUrl = "{{ url('hr/settings/job-titles') }}";

            // -------------------------------------------------------------------------
            // 1. Initialize Server-Side DataTables
            // -------------------------------------------------------------------------
            const table = $('#jobTitlesTable').DataTable({
                processing: true,
                serverSide: true,
                pagingType: 'simple_numbers',
                ajax: dataUrl,
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
                    },
                    {
                        data: 'name_ar',
                        name: 'name_ar',
                        render: data => `<span class="fw-semibold text-heading">${data}</span>`
                    },
                    {
                        data: 'name_en',
                        name: 'name_en',
                        render: data => `<span class="text-body">${data}</span>`
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: (data, type, row) => `
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-job-title"
                                    title="{{ __('Edit') }}"
                                    data-id="${row.id}"
                                    data-name-ar="${row.name_ar}"
                                    data-name-en="${row.name_en}"
                                    data-description-ar="${row.description_ar || ''}"
                                    data-description-en="${row.description_en || ''}"
                                    data-code="${row.code !== '-' ? row.code : ''}"
                                    data-department-id="${row.department_id || ''}"
                                    data-status="${row.status_raw || 'active'}">
                                <i class="icon-base bx bx-edit-alt"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-job-title"
                                    title="{{ __('Delete') }}"
                                    data-id="${row.id}"
                                    data-name="${row.name_ar}">
                                <i class="icon-base bx bx-trash"></i>
                            </button>
                        </div>`
                    }
                ],
                order: [[0, 'asc']]
            });

            // -------------------------------------------------------------------------
            // 2. Edit Action: Populate & Open Offcanvas
            // -------------------------------------------------------------------------
            const editOffcanvasEl = document.getElementById('offcanvasEditJobTitle');
            const editForm = document.getElementById('editJobTitleForm');

            $(document).on('click', '.btn-edit-job-title', function () {
                const id = $(this).data('id');
                const nameAr = $(this).data('name-ar');
                const nameEn = $(this).data('name-en');
                const descAr = $(this).data('description-ar');
                const descEn = $(this).data('description-en');
                const code = $(this).data('code');
                const departmentId = $(this).data('department-id');
                const status = $(this).data('status');

                editForm.action = baseUrl + '/' + id;
                $('#edit_name_ar').val(nameAr);
                $('#edit_name_en').val(nameEn);
                $('#edit_description_ar').val(descAr);
                $('#edit_description_en').val(descEn);
                $('#edit_code').val(code);
                $('#edit_department_id').val(departmentId).trigger('change');
                $('#edit_status').val(status);

                if (editOffcanvasEl) {
                    const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
                    editOffcanvas.show();
                }
            });

            // Initialize select2 in offcanvases if not already initialized
            if (typeof window.initSelect2 === 'function') {
                window.initSelect2();
            } else if ($.fn.select2) {
                $('.select2').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: $(this).data('placeholder') || '',
                            allowClear: $(this).data('allow-clear') !== false,
                            width: '100%',
                            dropdownParent: $(this).closest('.offcanvas').length ? $(this).closest('.offcanvas') : $(document.body)
                        });
                    }
                });
            }

            // -------------------------------------------------------------------------
            // 3. Delete Action: SweetAlert Confirmation
            // -------------------------------------------------------------------------
            $(document).on('click', '.btn-delete-job-title', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');

                let deleteUrl = baseUrl + '/' + id;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: "{{ __('Are you sure?') }}",
                        text: "{{ __('Are you sure you want to delete this job title: ') }} " + name + " ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6e7881',
                        confirmButtonText: "{{ __('Yes, delete it!') }}",
                        cancelButtonText: "{{ __('Cancel') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const deleteForm = $('#deleteJobTitleForm');
                            deleteForm.attr('action', deleteUrl);
                            deleteForm.submit();
                        }
                    });
                } else {
                    if (confirm("{{ __('Are you sure you want to delete this job title: ') }} " + name + " ?")) {
                        const deleteForm = $('#deleteJobTitleForm');
                        deleteForm.attr('action', deleteUrl);
                        deleteForm.submit();
                    }
                }
            });
        });
    </script>
@endsection