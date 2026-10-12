@extends('layouts/contentNavbarLayout')

@section('title', __('Event Types') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header -->
<div class="mb-4">
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
        {{ __('General Settings') }}
    </h4>
</div>

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <h6 class="alert-heading fw-bold mb-1">{{ __('Please review the following errors:') }}</h6>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4 d-flex align-items-center" role="alert">
        <i class="icon-base bx bx-check-circle me-2 font-20"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                    <i class="icon-base bx bx-calendar-event text-primary"></i>
                    {{ __('Event Types') }}
                </h5>
                <small class="text-muted">{{ __('Manage activity categories, types, details, and status.') }}</small>
            </div>

            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto" data-bs-toggle="offcanvas" data-bs-target="#addEventTypeModal">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Event Type') }}</span>
                </button>
            </div>
        </div>

        <!-- Event Types Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="eventTypesTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 70px;">{{ __('Image') }}</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Value / Code') }}</th>
                            <th>{{ __('Details / Description') }}</th>
                            <th style="width: 100px;">{{ __('Status') }}</th>
                            <th>{{ __('Created Date') }}</th>
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

<!-- Side Modal: Add Event Type -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
    id="addEventTypeModal" aria-labelledby="addEventTypeModalLabel" style="width: 480px; max-width: 95vw;">
    <form action="{{ route('settings.general.event-types.store') }}" method="POST" enctype="multipart/form-data" class="h-100 d-flex flex-column">
            @csrf
            <div class="offcanvas-header border-bottom py-3 px-4">
                <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="addEventTypeModalLabel">
                    <i class="icon-base bx bx-plus-circle text-primary"></i>
                    {{ __('Add New Event Type') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4">
                <div>
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Event Type Name (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" placeholder="مثال: رحلة ترفيهية / نشاط رياضي" required dir="rtl">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Event Type Name (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" placeholder="Example: Field Trip / Sports Activity" required dir="ltr">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Value / Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="text" name="value" class="form-control" placeholder="مثال: TRIP-01 / ACT-SPORTS" dir="ltr">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Logo / Image') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">PNG, JPG, WEBP (بحد أقصى 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Details / Description (Arabic)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <textarea name="description_ar" rows="3" class="form-control" placeholder="تفاصيل ووصف نوع الفاعلية بالعربية..." dir="rtl"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Details / Description (English)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <textarea name="description_en" rows="3" class="form-control" placeholder="Event type details & description in English..." dir="ltr"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" name="status" id="add_status" value="1" checked>
                            <label class="form-check-label fw-semibold" for="add_status">{{ __('Active Status') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 px-4 pb-4 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Event Type') }}</span>
                </button>
            </div>
        </form>
</div>

<!-- Side Modal: Edit Event Type -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1"
    id="editEventTypeModal" aria-labelledby="editEventTypeModalLabel" style="width: 480px; max-width: 95vw;">
    <form id="editEventTypeForm" method="POST" enctype="multipart/form-data" class="h-100 d-flex flex-column">
            @csrf
            @method('PUT')
            <div class="offcanvas-header border-bottom py-3 px-4">
                <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="editEventTypeModalLabel">
                    <i class="icon-base bx bx-edit-alt text-primary"></i>
                    {{ __('Edit Event Type') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4">
                <div>
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Event Type Name (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required dir="rtl">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Event Type Name (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_en" name="name_en" class="form-control" required dir="ltr">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Value / Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="text" id="edit_value" name="value" class="form-control" dir="ltr">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Change Logo / Image') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <div id="edit_image_preview_box" class="d-none mb-2 align-items-center gap-2">
                            <img id="edit_image_preview" src="" alt="Preview" class="rounded border object-fit-cover" width="48" height="48">
                            <span class="text-muted small">{{ __('Current Image') }}</span>
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">PNG, JPG, WEBP (بحد أقصى 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Details / Description (Arabic)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <textarea id="edit_description_ar" name="description_ar" rows="3" class="form-control" dir="rtl"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Details / Description (English)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <textarea id="edit_description_en" name="description_en" rows="3" class="form-control" dir="ltr"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" name="status" id="edit_status" value="1">
                            <label class="form-check-label fw-semibold" for="edit_status">{{ __('Active Status') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 px-4 pb-4 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Update Event Type') }}</span>
                </button>
            </div>
        </form>
</div>

<!-- Modal: Delete Event Type -->
<div class="modal fade" id="deleteEventTypeModal" tabindex="-1" aria-labelledby="deleteEventTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteEventTypeForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2 text-danger" id="deleteEventTypeModalLabel">
                    <i class="icon-base bx bx-trash"></i>
                    {{ __('Confirm Deletion') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="icon-base bx bx-error-circle text-danger mb-3" style="font-size: 54px;"></i>
                <h5 class="fw-bold mb-2">{{ __('Are you sure you want to delete this event type?') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('You are about to delete:') }} <strong id="delete_target_name" class="text-heading"></strong>
                </p>
                <small class="text-danger d-block mt-2">{{ __('This action cannot be undone.') }}</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-trash"></i>
                    <span>{{ __('Yes, Delete') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('settings.general.event-types.data') }}";
    const baseUrl = "{{ url('settings/general/event-types') }}";
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Initialize Server-Side DataTable
    const table = $('#eventTypesTable').DataTable({
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
                data: 'image_url', 
                name: 'image',
                orderable: false, 
                searchable: false,
                render: data => data 
                    ? `<img src="${data}" alt="Logo" class="rounded object-fit-cover border" width="36" height="36">`
                    : `<div class="avatar avatar-sm bg-label-primary rounded d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="icon-base bx bx-calendar-event font-18"></i></div>`
            },
            { 
                data: 'name_ar', 
                name: 'name_ar',
                render: (data, type, row) => `<button type="button" class="btn btn-link p-0 text-start fw-semibold text-heading btn-edit-item" data-id="${row.id}">${data || '-'}</button>`
            },
            { 
                data: 'name_en', 
                name: 'name_en',
                render: (data, type, row) => `<button type="button" class="btn btn-link p-0 text-start text-body btn-edit-item" data-id="${row.id}">${data || '-'}</button>`
            },
            { 
                data: 'value', 
                name: 'value',
                render: data => (data && data !== '-')
                    ? `<span class="badge bg-label-secondary font-monospace">${data}</span>`
                    : `<span class="text-muted">-</span>`
            },
            {
                data: 'description_ar',
                name: 'description_ar',
                orderable: false,
                render: (data, type, row) => {
                    const desc = data || row.description_en || '';
                    if (!desc) return `<span class="text-muted small">-</span>`;
                    const truncated = desc.length > 40 ? desc.substring(0, 40) + '...' : desc;
                    return `<span class="small text-body" title="${desc}">${truncated}</span>`;
                }
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: (data, type, row) => {
                    const isChecked = data ? 'checked' : '';
                    return `
                        <div class="form-check form-switch d-inline-block m-0">
                            <input class="form-check-input btn-toggle-status" type="checkbox" role="switch" data-id="${row.id}" ${isChecked}>
                        </div>
                    `;
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: data => `<small class="text-muted">${data}</small>`
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: (data, type, row) => `
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-item"
                                title="{{ __('Edit') }}"
                                data-id="${row.id}"
                                data-name-ar="${row.name_ar || ''}"
                                data-name-en="${row.name_en || ''}"
                                data-value="${row.value !== '-' ? (row.value || '') : ''}"
                                data-description-ar="${row.description_ar || ''}"
                                data-description-en="${row.description_en || ''}"
                                data-status="${row.status ? 1 : 0}"
                                data-image="${row.image_url || ''}">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-item"
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

    // Handle Edit Modal Population
    const editModalEl = document.getElementById('editEventTypeModal');
    const editModal = editModalEl ? new bootstrap.Offcanvas(editModalEl) : null;
    const editForm = document.getElementById('editEventTypeForm');
    const inputNameAr = document.getElementById('edit_name_ar');
    const inputNameEn = document.getElementById('edit_name_en');
    const inputValue = document.getElementById('edit_value');
    const inputDescAr = document.getElementById('edit_description_ar');
    const inputDescEn = document.getElementById('edit_description_en');
    const inputStatus = document.getElementById('edit_status');
    const previewBox = document.getElementById('edit_image_preview_box');
    const previewImg = document.getElementById('edit_image_preview');

    const openEdit = function (row) {
        const id = row.id;
        editForm.action = baseUrl + '/' + id;
        inputNameAr.value = row.name_ar || '';
        inputNameEn.value = row.name_en || '';
        inputValue.value = row.value !== '-' ? (row.value || '') : '';
        inputDescAr.value = row.description_ar || '';
        inputDescEn.value = row.description_en || '';
        inputStatus.checked = row.status == 1;

        const image = row.image_url || '';
        if (image) {
            previewImg.src = image;
            previewBox.classList.remove('d-none');
            previewBox.classList.add('d-flex');
        } else {
            previewBox.classList.remove('d-flex');
            previewBox.classList.add('d-none');
        }

        if (editModal) editModal.show();
    };

    $(document).on('click', '.btn-edit-item', function () {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(item => String(item.id) === String(id));
        if (row) openEdit(row);
    });

    // Handle Delete Modal Population
    const deleteModalEl = document.getElementById('deleteEventTypeModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteEventTypeForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-item', function () {
        const id = $(this).data('id');
        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = $(this).data('name') || '';
        if (deleteModal) deleteModal.show();
    });

    // Handle Status Toggle Ajax
    $(document).on('change', '.btn-toggle-status', function () {
        const id = $(this).data('id');
        const checkbox = $(this);
        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            type: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            error: function () {
                checkbox.prop('checked', !checkbox.prop('checked'));
                alert('{{ __("Failed to update status. Please try again.") }}');
            }
        });
    });
});
</script>
@endsection
