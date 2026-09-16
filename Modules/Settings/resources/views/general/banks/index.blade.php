@extends('layouts/contentNavbarLayout')

@section('title', __('Banks') . ' - ' . config('variables.templateName'))

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
                        <i class="icon-base bx bxs-bank text-primary"></i>
                        {{ __('Banks') }}
                    </h5>
                </div>

                <div>
                    <button type="button"
                        class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddBank"
                        aria-controls="offcanvasAddBank">
                        <i class="icon-base bx bx-plus font-18"></i>
                        <span>{{ __('Add New Bank') }}</span>
                    </button>
                </div>
            </div>

            <!-- Banks Server-Side DataTable Card -->
            <div class="card shadow-sm border-0">
                <div class="card-datatable">
                    <table id="banksTable" class="table table-hover border-top w-100 text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>{{ __('Arabic Name') }}</th>
                                <th>{{ __('English Name') }}</th>
                                <th>{{ __('SWIFT Code') }}</th>
                                <th>{{ __('Bank Code') }}</th>
                                <th>{{ __('Branches Count') }}</th>
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
    <!-- 1. Offcanvas: Add New Bank                                                -->
    <!-- ========================================================================= -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
        id="offcanvasAddBank" aria-labelledby="offcanvasAddBankLabel" style="width: 480px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasAddBankLabel"
                class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bxs-bank text-primary"></i>
                <span>{{ __('Add New Bank') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4">
            <form action="{{ route('settings.general.banks.store') }}" method="POST" id="addBankForm">
                @csrf

                <!-- Bank Name [Arabic] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Bank Name [Arabic]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control"
                        placeholder="{{ __('e.g. National Bank of Kuwait') }}" required dir="rtl">
                </div>

                <!-- Bank Name [English] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Bank Name [English]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control"
                        placeholder="e.g. National Bank of Kuwait" required dir="ltr">
                </div>

                <!-- Bank Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Bank Code') }}</label>
                    <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. NBK') }}" dir="ltr">
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
    <!-- 2. Offcanvas: Edit Bank                                                   -->
    <!-- ========================================================================= -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
        id="offcanvasEditBank" aria-labelledby="offcanvasEditBankLabel" style="width: 480px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 id="offcanvasEditBankLabel"
                class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Bank') }}</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editBankForm">
                @csrf
                @method('PUT')

                <!-- Bank Name [Arabic] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Bank Name [Arabic]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="edit_bank_name_ar" name="name_ar" class="form-control" required dir="rtl">
                </div>

                <!-- Bank Name [English] -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Bank Name [English]') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="edit_bank_name_en" name="name_en" class="form-control" required dir="ltr">
                </div>

                <!-- Bank Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Bank Code') }}</label>
                    <input type="text" id="edit_bank_code" name="code" class="form-control" dir="ltr">
                </div>

                <!-- SWIFT Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('SWIFT Code') }}</label>
                    <input type="text" id="edit_bank_swift_code" name="swift_code" class="form-control" dir="ltr">
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select select2" id="edit_bank_status" name="status" required>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

                <!-- Address [Arabic] -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Address [Arabic]') }}</label>
                    <textarea id="edit_bank_address_ar" name="address_ar" class="form-control" rows="2" dir="rtl"></textarea>
                </div>

                <!-- Address [English] -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('Address [English]') }}</label>
                    <textarea id="edit_bank_address_en" name="address_en" class="form-control" rows="2" dir="ltr"></textarea>
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
    <form id="deleteBankForm" action="" method="POST" style="display: none;">
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

            const dataUrl = "{{ route('settings.general.banks.data') }}";
            const baseUrl = "{{ url('settings/general/banks') }}";

            // -------------------------------------------------------------------------
            // 1. Initialize Server-Side DataTables
            // -------------------------------------------------------------------------
            const table = $('#banksTable').DataTable({
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
                        data: 'branches_count',
                        name: 'branches_count',
                        searchable: false,
                        render: data => `<span class="badge bg-label-primary rounded-pill px-2"><i class="icon-base bx bx-git-repo-forked me-1"></i>${data}</span>`
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
                                <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-bank"
                                        title="{{ __('Edit') }}"
                                        data-id="${row.id}"
                                        data-name-ar="${row.name_ar}"
                                        data-name-en="${row.name_en}"
                                        data-code="${row.code !== '-' ? row.code : ''}"
                                        data-swift-code="${row.swift_code ? row.swift_code.replace(/<[^>]*>?/gm, '') : ''}"
                                        data-status="${row.status_raw || 'active'}"
                                        data-address-ar="${row.address_ar || ''}"
                                        data-address-en="${row.address_en || ''}">
                                    <i class="icon-base bx bx-edit-alt"></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-bank"
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
            // 2. Edit Action: Populate & Open Offcanvas
            // -------------------------------------------------------------------------
            const editOffcanvasEl = document.getElementById('offcanvasEditBank');
            const editForm = document.getElementById('editBankForm');

            $(document).on('click', '.btn-edit-bank', function () {
                const id = $(this).data('id');
                const nameAr = $(this).data('name-ar');
                const nameEn = $(this).data('name-en');
                const code = $(this).data('code');
                const swiftCode = $(this).data('swift-code');
                const status = $(this).data('status');
                const addressAr = $(this).data('address-ar');
                const addressEn = $(this).data('address-en');

                editForm.action = baseUrl + '/' + id;
                $('#edit_bank_name_ar').val(nameAr);
                $('#edit_bank_name_en').val(nameEn);
                $('#edit_bank_code').val(code);
                $('#edit_bank_swift_code').val(swiftCode);
                $('#edit_bank_status').val(status).trigger('change');
                $('#edit_bank_address_ar').val(addressAr);
                $('#edit_bank_address_en').val(addressEn);

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
            $(document).on('click', '.btn-delete-bank', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');

                let deleteUrl = baseUrl + '/' + id;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: "{{ __('Are you sure?') }}",
                        text: "{{ __('Are you sure you want to delete this bank: ') }} " + name + " ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6e7881',
                        confirmButtonText: "{{ __('Yes, delete it!') }}",
                        cancelButtonText: "{{ __('Cancel') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const deleteForm = $('#deleteBankForm');
                            deleteForm.attr('action', deleteUrl);
                            deleteForm.submit();
                        }
                    });
                } else {
                    if (confirm("{{ __('Are you sure you want to delete this bank: ') }} " + name + " ?")) {
                        const deleteForm = $('#deleteBankForm');
                        deleteForm.attr('action', deleteUrl);
                        deleteForm.submit();
                    }
                }
            });
        });
    </script>
@endsection
