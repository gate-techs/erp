@extends('layouts/contentNavbarLayout')

@section('title', __('Bank Branches') . ' - ' . config('variables.templateName'))

@section('content')
    <!-- Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
            {{ __('General Settings') }}
        </h4>
    </div>

    <!-- 2-Column Layout with Sub-Sidebar -->
    <div class="row g-4">
        <!-- Sub-Sidebar Column -->
        <div class="col-12 col-md-4 col-lg-3">
            @include('settings::general.partials.sidebar')
        </div>

        <!-- Main Content Column -->
        <div class="col-12 col-md-8 col-lg-9">
            <!-- Action Bar: Title & Add Button -->
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                        <i class="icon-base bx bx-git-repo-forked text-primary"></i>
                        {{ __('Bank Branches') }}
                    </h5>
                </div>

                <div>
                    <button type="button"
                        class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddBranch"
                        aria-controls="offcanvasAddBranch">
                        <i class="icon-base bx bx-plus font-18"></i>
                        <span>{{ __('Add New Bank Branch') }}</span>
                    </button>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body py-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-5">
                            <label class="form-label small text-muted mb-1">{{ __('Filter by Bank') }}</label>
                            <select id="filter_parent_id" class="form-select select2" data-placeholder="{{ __('All Banks') }}" data-allow-clear="true">
                                <option value="">{{ __('All Banks') }}</option>
                                @foreach ($mainBanks as $mainBank)
                                    <option value="{{ $mainBank->id }}">{{ $mainBank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-muted mb-1">{{ __('Filter by Status') }}</label>
                            <select id="filter_status" class="form-select select2" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3 text-md-end mt-md-4">
                            <button type="button" id="btn_reset_filters" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">
                                <i class="icon-base bx bx-reset me-1"></i> {{ __('Reset Filters') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Branches Server-Side DataTable Card -->
            <div class="card shadow-sm border-0">
                <div class="card-datatable">
                    <table id="branchesTable" class="table table-hover border-top w-100 text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>{{ __('Main Bank') }}</th>
                                <th>{{ __('Branch Arabic Name') }}</th>
                                <th>{{ __('Branch English Name') }}</th>
                                <th>{{ __('SWIFT Code') }}</th>
                                <th>{{ __('Branch Code') }}</th>
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
    <!-- 1. Offcanvas: Add New Branch                                              -->
    <!-- ========================================================================= -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
        id="offcanvasAddBranch" aria-labelledby="offcanvasAddBranchLabel" style="width: 480px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasAddBranchLabel"
                class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-git-repo-forked text-primary"></i>
                <span>{{ __('Add New Bank Branch') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4">
            <form action="{{ route('settings.general.bank-branches.store') }}" method="POST" id="addBranchForm">
                @csrf

                <!-- Main Bank Selection -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Main Bank') }} <span
                            class="text-danger">*</span></label>
                    <select name="parent_id" id="add_branch_parent_id" class="form-select select2" data-placeholder="{{ __('Select Main Bank') }}" required>
                        <option value=""></option>
                        @foreach ($mainBanks as $mainBank)
                            <option value="{{ $mainBank->id }}">{{ $mainBank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Branch Name [Arabic] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Branch Name [Arabic]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control"
                        placeholder="{{ __('e.g. Hawally Branch') }}" required dir="rtl">
                </div>

                <!-- Branch Name [English] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Branch Name [English]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control"
                        placeholder="e.g. Hawally Branch" required dir="ltr">
                </div>

                <!-- Branch Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Branch Code') }}</label>
                    <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. 001') }}" dir="ltr">
                </div>

                <!-- SWIFT Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('SWIFT Code') }}</label>
                    <input type="text" name="swift_code" class="form-control" placeholder="{{ __('e.g. NBKOKWKW') }}" dir="ltr">
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select select2" name="status" required>
                        <option value="active" selected>{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

                <!-- Address [Arabic] -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Address [Arabic]') }}</label>
                    <textarea name="address_ar" class="form-control" rows="2"
                        placeholder="{{ __('Address (Arabic)') }}" dir="rtl"></textarea>
                </div>

                <!-- Address [English] -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('Address [English]') }}</label>
                    <textarea name="address_en" class="form-control" rows="2"
                        placeholder="{{ __('Address (English)') }}" dir="ltr"></textarea>
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
    <!-- 2. Offcanvas: Edit Branch                                                 -->
    <!-- ========================================================================= -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
        id="offcanvasEditBranch" aria-labelledby="offcanvasEditBranchLabel" style="width: 480px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasEditBranchLabel"
                class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Bank Branch') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editBranchForm">
                @csrf
                @method('PUT')

                <!-- Main Bank Selection -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Main Bank') }} <span
                            class="text-danger">*</span></label>
                    <select name="parent_id" id="edit_branch_parent_id" class="form-select select2" data-placeholder="{{ __('Select Main Bank') }}" required>
                        <option value=""></option>
                        @foreach ($mainBanks as $mainBank)
                            <option value="{{ $mainBank->id }}">{{ $mainBank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Branch Name [Arabic] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Branch Name [Arabic]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="edit_branch_name_ar" name="name_ar" class="form-control" required dir="rtl">
                </div>

                <!-- Branch Name [English] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Branch Name [English]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="edit_branch_name_en" name="name_en" class="form-control" required dir="ltr">
                </div>

                <!-- Branch Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Branch Code') }}</label>
                    <input type="text" id="edit_branch_code" name="code" class="form-control" dir="ltr">
                </div>

                <!-- SWIFT Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('SWIFT Code') }}</label>
                    <input type="text" id="edit_branch_swift_code" name="swift_code" class="form-control" dir="ltr">
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select select2" id="edit_branch_status" name="status" required>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

                <!-- Address [Arabic] -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Address [Arabic]') }}</label>
                    <textarea id="edit_branch_address_ar" name="address_ar" class="form-control" rows="2" dir="rtl"></textarea>
                </div>

                <!-- Address [English] -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('Address [English]') }}</label>
                    <textarea id="edit_branch_address_en" name="address_en" class="form-control" rows="2" dir="ltr"></textarea>
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
    <form id="deleteBranchForm" action="" method="POST" style="display: none;">
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

            const dataUrl = "{{ route('settings.general.bank-branches.data') }}";
            const baseUrl = "{{ url('settings/general/bank-branches') }}";

            // -------------------------------------------------------------------------
            // 1. Initialize Server-Side DataTables
            // -------------------------------------------------------------------------
            const table = $('#branchesTable').DataTable({
                processing: true,
                serverSide: true,
                pagingType: 'simple_numbers',
                ajax: {
                    url: dataUrl,
                    data: function (d) {
                        d.parent_id = $('#filter_parent_id').val();
                        d.status = $('#filter_status').val();
                    }
                },
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
                    },
                    { 
                        data: 'bank_name', 
                        name: 'parent_id',
                        render: data => `<span class="badge bg-label-primary fs-7"><i class="icon-base bx bxs-bank me-1"></i>${data}</span>`
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
                        data: 'swift_code', 
                        name: 'swift_code'
                    },
                    { 
                        data: 'code', 
                        name: 'code',
                        render: data => data !== '-'
                            ? `<span class="badge bg-label-secondary font-monospace">${data}</span>`
                            : `<span class="text-muted">-</span>`
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
                                <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-branch"
                                        title="{{ __('Edit') }}"
                                        data-id="${row.id}"
                                        data-parent-id="${row.parent_id || ''}"
                                        data-name-ar="${row.name_ar}"
                                        data-name-en="${row.name_en}"
                                        data-code="${row.code !== '-' ? row.code : ''}"
                                        data-swift-code="${row.swift_code ? row.swift_code.replace(/<[^>]*>?/gm, '') : ''}"
                                        data-status="${row.status_raw || 'active'}"
                                        data-address-ar="${row.address_ar || ''}"
                                        data-address-en="${row.address_en || ''}">
                                    <i class="icon-base bx bx-edit-alt"></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-branch"
                                        title="{{ __('Delete') }}"
                                        data-id="${row.id}"
                                        data-name="${row.name_ar}">
                                    <i class="icon-base bx bx-trash"></i>
                                </button>
                            </div>`
                    }
                ],
                order: [[0, 'desc']]
            });

            // -------------------------------------------------------------------------
            // Filter Handlers
            // -------------------------------------------------------------------------
            $('#filter_parent_id, #filter_status').on('change', function () {
                table.ajax.reload();
            });

            $('#btn_reset_filters').on('click', function () {
                $('#filter_parent_id').val('').trigger('change');
                $('#filter_status').val('').trigger('change');
                table.ajax.reload();
            });

            // -------------------------------------------------------------------------
            // 2. Edit Action: Populate & Open Offcanvas
            // -------------------------------------------------------------------------
            const editOffcanvasEl = document.getElementById('offcanvasEditBranch');
            const editForm = document.getElementById('editBranchForm');

            $(document).on('click', '.btn-edit-branch', function () {
                const id = $(this).data('id');
                const parentId = $(this).data('parent-id');
                const nameAr = $(this).data('name-ar');
                const nameEn = $(this).data('name-en');
                const code = $(this).data('code');
                const swiftCode = $(this).data('swift-code');
                const status = $(this).data('status');
                const addressAr = $(this).data('address-ar');
                const addressEn = $(this).data('address-en');

                editForm.action = baseUrl + '/' + id;
                $('#edit_branch_parent_id').val(parentId).trigger('change');
                $('#edit_branch_name_ar').val(nameAr);
                $('#edit_branch_name_en').val(nameEn);
                $('#edit_branch_code').val(code);
                $('#edit_branch_swift_code').val(swiftCode);
                $('#edit_branch_status').val(status).trigger('change');
                $('#edit_branch_address_ar').val(addressAr);
                $('#edit_branch_address_en').val(addressEn);

                if (editOffcanvasEl) {
                    const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
                    editOffcanvas.show();
                }
            });

            // Initialize select2 inside offcanvases
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
            $(document).on('click', '.btn-delete-branch', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');

                let deleteUrl = baseUrl + '/' + id;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: "{{ __('Are you sure?') }}",
                        text: "{{ __('Are you sure you want to delete this branch: ') }} " + name + " ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6e7881',
                        confirmButtonText: "{{ __('Yes, delete it!') }}",
                        cancelButtonText: "{{ __('Cancel') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const deleteForm = $('#deleteBranchForm');
                            deleteForm.attr('action', deleteUrl);
                            deleteForm.submit();
                        }
                    });
                } else {
                    if (confirm("{{ __('Are you sure you want to delete this branch: ') }} " + name + " ?")) {
                        const deleteForm = $('#deleteBranchForm');
                        deleteForm.attr('action', deleteUrl);
                        deleteForm.submit();
                    }
                }
            });
        });
    </script>
@endsection
