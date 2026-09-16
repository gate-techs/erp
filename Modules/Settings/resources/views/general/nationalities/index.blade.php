@extends('layouts/contentNavbarLayout')

@section('title', __('Nationalities') . ' - ' . config('variables.templateName'))

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
                    <i class="icon-base bx bx-flag text-primary"></i>
                    {{ __('Nationalities') }}
                </h5>
        
            </div>

            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#addNationalityModal">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Nationality') }}</span>
                </button>
            </div>
        </div>

        <!-- Nationalities Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="nationalitiesTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 70px;">{{ __('Logo') }}</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Code / Symbol') }}</th>
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

<!-- Modal: Add Nationality -->
<div class="modal fade" id="addNationalityModal" tabindex="-1" aria-labelledby="addNationalityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('settings.general.nationalities.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="addNationalityModalLabel">
                    <i class="icon-base bx bx-plus-circle text-primary"></i>
                    {{ __('Add New Nationality') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required">{{ __('Nationality Name (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" placeholder="مثال: كويتي" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label required">{{ __('Nationality Name (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" placeholder="Example: Kuwaiti" required dir="ltr">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Short Code / Symbol') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="text" name="value" class="form-control" placeholder="مثال: KWT" dir="ltr">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Logo / Image') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">PNG, JPG, WEBP (بحد أقصى 2MB)</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Nationality') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Nationality -->
<div class="modal fade" id="editNationalityModal" tabindex="-1" aria-labelledby="editNationalityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editNationalityForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="editNationalityModalLabel">
                    <i class="icon-base bx bx-edit-alt text-primary"></i>
                    {{ __('Edit Nationality') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required">{{ __('Nationality Name (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label required">{{ __('Nationality Name (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_en" name="name_en" class="form-control" required dir="ltr">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Short Code / Symbol') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="text" id="edit_value" name="value" class="form-control" dir="ltr">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Change Logo / Image') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <div id="edit_image_preview_box" class="d-none mb-2 align-items-center gap-2">
                            <img id="edit_image_preview" src="" alt="Preview" class="rounded border object-fit-cover" width="48" height="48">
                            <span class="text-muted small">{{ __('Current Image') }}</span>
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">PNG, JPG, WEBP (بحد أقصى 2MB)</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Delete Nationality -->
<div class="modal fade" id="deleteNationalityModal" tabindex="-1" aria-labelledby="deleteNationalityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteNationalityForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete the nationality:') }}
                    <br>
                    <strong id="delete_target_name" class="text-danger"></strong>؟
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
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

    const dataUrl = "{{ route('settings.general.nationalities.data') }}";
    const baseUrl = "{{ url('settings/general/nationalities') }}";

    // Initialize Server-Side DataTable
    const table = $('#nationalitiesTable').DataTable({
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
                    ? `<img src="${data}" alt="Flag" class="rounded object-fit-cover border" width="36" height="36">`
                    : `<div class="avatar avatar-sm bg-label-primary rounded d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="icon-base bx bx-flag font-18"></i></div>`
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
                data: 'value', 
                name: 'value',
                render: data => data !== '-'
                    ? `<span class="badge bg-label-secondary font-monospace">${data}</span>`
                    : `<span class="text-muted">-</span>`
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
                                data-name-ar="${row.name_ar}"
                                data-name-en="${row.name_en}"
                                data-value="${row.value !== '-' ? row.value : ''}"
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
    const editModalEl = document.getElementById('editNationalityModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editForm = document.getElementById('editNationalityForm');
    const inputNameAr = document.getElementById('edit_name_ar');
    const inputNameEn = document.getElementById('edit_name_en');
    const inputValue = document.getElementById('edit_value');
    const previewBox = document.getElementById('edit_image_preview_box');
    const previewImg = document.getElementById('edit_image_preview');

    $(document).on('click', '.btn-edit-item', function () {
        const id = $(this).data('id');
        editForm.action = baseUrl + '/' + id;
        inputNameAr.value = $(this).data('name-ar') || '';
        inputNameEn.value = $(this).data('name-en') || '';
        inputValue.value = $(this).data('value') || '';

        const image = $(this).data('image');
        if (image) {
            previewImg.src = image;
            previewBox.classList.remove('d-none');
            previewBox.classList.add('d-flex');
        } else {
            previewBox.classList.remove('d-flex');
            previewBox.classList.add('d-none');
        }

        if (editModal) editModal.show();
    });

    // Handle Delete Modal Population
    const deleteModalEl = document.getElementById('deleteNationalityModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteNationalityForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-item', function () {
        const id = $(this).data('id');
        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = $(this).data('name') || '';
        if (deleteModal) deleteModal.show();
    });
});
</script>
@endsection
