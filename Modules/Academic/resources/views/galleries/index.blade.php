@extends('layouts/contentNavbarLayout')

@section('title', __('Student Galleries') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Student Services') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Student Galleries') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-images text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Student Galleries') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        @can('academic.galleries.create')
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" id="btn_open_add_modal">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Album') }}</span>
        </button>
        @endcan
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Nursery / Institution') }}</label>
                <select id="filter_institution_id" class="select2 form-select" data-placeholder="{{ __('All Institutions') }}">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Stage Filter -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Stage') }}</label>
                <select id="filter_academic_stage_id" class="select2 form-select" data-placeholder="{{ __('All Stages') }}">
                    <option value="">{{ __('All Stages') }}</option>
                    @foreach ($academicStages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Class Filter -->
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Classroom') }}</label>
                <select id="filter_class_room_id" class="select2 form-select" data-placeholder="{{ __('All Classes') }}">
                    <option value="">{{ __('All Classes') }}</option>
                    @foreach ($academicClasses as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Reset Button -->
            <div class="col-12 col-md-2">
                <button type="button" id="btn_reset_filters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="galleriesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Photo Gallery') }}</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Educational Stage') }}</th>
                    <th>{{ __('Classroom') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Added By') }}</th>
                    <th>{{ __('Nursery / Institution') }}</th>
                    <th class="text-center">{{ __('Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Offcanvas: Side Modal for Add / Edit Gallery                               -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasGallery" aria-labelledby="offcanvasGalleryLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasGalleryLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-images text-primary font-22" id="modal_icon"></i>
            <span id="modal_title">{{ __('Add New Album') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="galleryForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="gallery_id" id="gallery_id">

            <!-- 1. Classroom Selection (Required) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Classroom') }} <span class="text-danger">*</span></label>
                <select name="class_room_id" id="gallery_class_room_id" class="form-select select2-offcanvas" required data-placeholder="{{ __('Select Classroom') }}">
                    <option value="">{{ __('Select Classroom') }}</option>
                    @foreach ($academicClasses as $cls)
                        @php
                            $className = $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name;
                            $stageName = $cls->academicStage?->translate(app()->getLocale())?->name ?? $cls->academicStage?->name ?? '';
                        @endphp
                        <option value="{{ $cls->id }}">{{ $className }}{{ $stageName ? ' (' . $stageName . ')' : '' }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block text-danger font-12" id="error_class_room_id"></div>
            </div>

            <!-- 2. Teacher (Optional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Teacher') }}</label>
                <select name="teacher_id" id="gallery_teacher_id" class="form-select select2-offcanvas" data-placeholder="{{ __('Select Teacher') }}">
                    <option value="">{{ __('Admin / Management') }}</option>
                    @foreach ($teachers as $teacher)
                        @php
                            $teacherName = $teacher->translate(app()->getLocale())?->name ?? $teacher->translate('ar')?->name ?? $teacher->name;
                        @endphp
                        <option value="{{ $teacher->id }}">{{ $teacherName }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block text-danger font-12" id="error_teacher_id"></div>
            </div>

            <!-- 3. Sharing Scope Selector -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading d-block mb-2">{{ __('Sharing Scope') }}</label>
                <div class="btn-group w-100" role="group" aria-label="Sharing Scope">
                    <input type="radio" class="btn-check" name="scope_option" id="scope_class" value="class" checked autocomplete="off">
                    <label class="btn btn-outline-primary font-12 py-2" for="scope_class">
                        <i class="icon-base bx bx-group me-1"></i>{{ __('All Class Students') }}
                    </label>

                    <input type="radio" class="btn-check" name="scope_option" id="scope_single" value="single" autocomplete="off">
                    <label class="btn btn-outline-primary font-12 py-2" for="scope_single">
                        <i class="icon-base bx bx-user me-1"></i>{{ __('Single Student') }}
                    </label>

                    <input type="radio" class="btn-check" name="scope_option" id="scope_selected" value="selected" autocomplete="off">
                    <label class="btn btn-outline-primary font-12 py-2" for="scope_selected">
                        <i class="icon-base bx bx-user-check me-1"></i>{{ __('Selected Students') }}
                    </label>
                </div>
            </div>

            <!-- 3.A Single Student Select Container -->
            <div class="mb-3 d-none" id="single_student_box">
                <label class="form-label required fw-semibold text-heading">{{ __('Student') }} <span class="text-danger">*</span></label>
                <select name="student_id" id="gallery_student_id" class="form-select select2-offcanvas" data-placeholder="{{ __('Select Student') }}">
                    <option value="">{{ __('Select Student') }}</option>
                </select>
                <div class="invalid-feedback d-block text-danger font-12" id="error_student_id"></div>
            </div>

            <!-- 3.B Multi-Students Select Container -->
            <div class="mb-3 d-none" id="selected_students_box">
                <label class="form-label required fw-semibold text-heading">{{ __('Selected Students') }} <span class="text-danger">*</span></label>
                <select name="student_ids[]" id="gallery_student_ids" class="form-select select2-offcanvas" multiple data-placeholder="{{ __('Select Students') }}">
                </select>
                <div class="invalid-feedback d-block text-danger font-12" id="error_student_ids"></div>
            </div>

            <!-- 4. Date & Title in Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold text-heading">{{ __('Date') }}</label>
                    <input type="date" name="date" id="gallery_date" class="form-control" value="{{ date('Y-m-d') }}">
                    <div class="invalid-feedback d-block text-danger font-12" id="error_date"></div>
                </div>
                <div class="col-12 col-md-7">
                    <label class="form-label fw-semibold text-heading">{{ __('Gallery Title') }}</label>
                    <input type="text" name="title" id="gallery_title" class="form-control" placeholder="{{ __('Gallery Title') }}">
                    <div class="invalid-feedback d-block text-danger font-12" id="error_title"></div>
                </div>
            </div>

            <!-- 5. Description -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description') }}</label>
                <textarea name="description" id="gallery_description" class="form-control" rows="2" placeholder="{{ __('Description') }}"></textarea>
                <div class="invalid-feedback d-block text-danger font-12" id="error_description"></div>
            </div>

            <!-- 6. Existing Images Preview (Only on Edit) -->
            <div class="mb-3 d-none" id="existing_images_section">
                <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-2">
                    <span>{{ __('Current Photos') }}</span>
                    <span class="badge bg-label-primary font-11" id="existing_images_count">0</span>
                </label>
                <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-light" id="existing_images_container" style="max-height: 180px; overflow-y: auto;">
                    <!-- Rendered dynamically -->
                </div>
            </div>

            <!-- 7. Media Upload -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between">
                    <span><i class="icon-base bx bx-cloud-upload me-1"></i>{{ __('Upload Photos & Videos') }}</span>
                    <small class="text-muted font-11">{{ __('Multiple files allowed') }}</small>
                </label>
                <input type="file" name="images[]" id="gallery_images_input" class="form-control" multiple accept="image/*,video/*">
                <div class="invalid-feedback d-block text-danger font-12" id="error_images"></div>
            </div>

            <!-- Actions Buttons -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_save_gallery" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span id="btn_save_text">{{ __('Save Album') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Delete Confirmation Modal                                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content shadow border-0" style="border-radius: 8px;">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-trash font-28 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Delete') }}</h5>
                <p class="text-muted font-13 mb-4">{{ __('Are you sure you want to delete this gallery?') }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="btn_confirm_delete" class="btn btn-danger px-4">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gallery-thumb-item {
    transition: transform 0.15s ease;
}
.gallery-thumb-item:hover {
    transform: scale(1.08);
}
.existing-img-wrapper {
    position: relative;
    width: 65px;
    height: 65px;
    border-radius: 6px;
    overflow: hidden;
}
.existing-img-wrapper img, .existing-img-wrapper video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.existing-img-del-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 20px;
    height: 20px;
    background: rgba(220, 53, 69, 0.85);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    transition: background 0.15s;
}
.existing-img-del-btn:hover {
    background: #dc3545;
}
</style>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    function initGalleries() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initGalleries, 50);
            return;
        }

        const baseUrl = "{{ url('academic/galleries') }}";
        const dataUrl = "{{ route('academic.galleries.data') }}";
        let targetDeleteId = null;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // Toast Notification Helper
        function showToast(type, message, title = '') {
            const mappedType = type === 'danger' ? 'error' : type;
            if (window.toastr && typeof window.toastr[mappedType] === 'function') {
                window.toastr[mappedType](message, title);
            } else if (window.notify && typeof window.notify[mappedType] === 'function') {
                window.notify[mappedType](message, title);
            } else if (window.Swal) {
                window.Swal.fire({
                    icon: type === 'danger' ? 'error' : type,
                    title: message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert(message);
            }
        }

        // Initialize Select2 Filters
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function () {
                    return $(this).data('placeholder') || '';
                },
                allowClear: true,
                width: '100%',
                dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            });

            $('.select2-offcanvas').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#offcanvasGallery'),
                placeholder: function () {
                    return $(this).data('placeholder') || '';
                },
                allowClear: true,
                width: '100%',
                dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            });
        }

        // Offcanvas Instance
        const offcanvasEl = document.getElementById('offcanvasGallery');
        const offcanvas = new bootstrap.Offcanvas(offcanvasEl);

        // Delete Modal Instance
        const deleteModalEl = document.getElementById('deleteGalleryModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);

        // -------------------------------------------------------------
        // DataTable Initialization
        // -------------------------------------------------------------
        const table = $('#galleriesTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-images font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No records found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add New Album" to create the first album.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id     = $('#filter_institution_id').val();
                    d.branch_id          = $('#filter_branch_id').val();
                    d.academic_stage_id  = $('#filter_academic_stage_id').val();
                    d.class_room_id      = $('#filter_class_room_id').val();
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-center',
                    render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
                },
                { data: 'gallery_images', name: 'gallery_images', orderable: false, searchable: false },
                { data: 'student_name', name: 'student_id' },
                { data: 'stage_name', name: 'stage_name', orderable: false },
                { data: 'class_name', name: 'class_room_id' },
                { data: 'branch_name', name: 'branch_name', orderable: false },
                { data: 'added_by', name: 'created_by' },
                { data: 'institution_name', name: 'institution_name', orderable: false },
                { data: 'date_formatted', name: 'date', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        // Filters Change Events
        $('#filter_institution_id, #filter_branch_id, #filter_academic_stage_id, #filter_class_room_id').on('change', function () {
            table.ajax.reload();
        });

        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_academic_stage_id').val('').trigger('change');
            $('#filter_class_room_id').val('').trigger('change');
            table.ajax.reload();
        });

        // -------------------------------------------------------------
        // Dynamic Class Students Loading
        // -------------------------------------------------------------
        function loadClassStudents(classroomId, selectedStudentId = null, selectedStudentIds = []) {
            const studentSelect = $('#gallery_student_id');
            const studentsSelect = $('#gallery_student_ids');

            studentSelect.empty().append('<option value="">{{ __("Select Student") }}</option>');
            studentsSelect.empty();

            if (!classroomId) {
                studentSelect.trigger('change');
                studentsSelect.trigger('change');
                return;
            }

            $.ajax({
                url: `${baseUrl}/class-students/${classroomId}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        res.data.forEach(function (std) {
                            const optText = std.name + (std.code ? ` (${std.code})` : '');
                            
                            // For Single Student Select
                            const isSingleSelected = selectedStudentId && selectedStudentId == std.id;
                            studentSelect.append(new Option(optText, std.id, isSingleSelected, isSingleSelected));

                            // For Multi Students Select
                            const isMultiSelected = selectedStudentIds && selectedStudentIds.includes(std.id);
                            studentsSelect.append(new Option(optText, std.id, isMultiSelected, isMultiSelected));
                        });

                        studentSelect.trigger('change');
                        studentsSelect.trigger('change');
                    }
                }
            });
        }

        // On Classroom Select Change
        $('#gallery_class_room_id').on('change', function () {
            const classId = $(this).val();
            loadClassStudents(classId);
        });

        // Sharing Scope Toggle handler
        $('input[name="scope_option"]').on('change', function () {
            const val = $(this).val();
            if (val === 'single') {
                $('#single_student_box').removeClass('d-none');
                $('#selected_students_box').addClass('d-none');
            } else if (val === 'selected') {
                $('#selected_students_box').removeClass('d-none');
                $('#single_student_box').addClass('d-none');
            } else {
                $('#single_student_box').addClass('d-none');
                $('#selected_students_box').addClass('d-none');
            }
        });

        // -------------------------------------------------------------
        // Open Add Modal
        // -------------------------------------------------------------
        $('#btn_open_add_modal').on('click', function () {
            $('#galleryForm')[0].reset();
            $('#gallery_id').val('');
            $('#gallery_class_room_id').val('').trigger('change');
            $('#gallery_teacher_id').val('').trigger('change');
            $('#scope_class').prop('checked', true).trigger('change');
            $('#existing_images_section').addClass('d-none');
            $('#existing_images_container').empty();
            clearValidationErrors();

            $('#modal_title').text('{{ __("Add New Album") }}');
            $('#btn_save_text').text('{{ __("Save Album") }}');
            offcanvas.show();
        });

        // -------------------------------------------------------------
        // Open Edit Modal
        // -------------------------------------------------------------
        $(document).on('click', '.edit-gallery-btn', function () {
            const id = $(this).data('id');
            clearValidationErrors();

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (!res.success || !res.data) return;
                    const data = res.data;

                    $('#gallery_id').val(data.id);
                    $('#gallery_title').val(data.title || '');
                    $('#gallery_description').val(data.description || '');
                    $('#gallery_date').val(data.date || '');
                    $('#gallery_teacher_id').val(data.teacher_id || '').trigger('change');
                    $('#gallery_class_room_id').val(data.class_room_id || '').trigger('change');

                    // Set Scope
                    if (data.sharing_scope === 'single') {
                        $('#scope_single').prop('checked', true).trigger('change');
                    } else if (data.sharing_scope === 'selected') {
                        $('#scope_selected').prop('checked', true).trigger('change');
                    } else {
                        $('#scope_class').prop('checked', true).trigger('change');
                    }

                    // Load Students with pre-selection
                    loadClassStudents(data.class_room_id, data.student_id, data.selected_student_ids || []);

                    // Render existing images
                    const container = $('#existing_images_container');
                    container.empty();
                    if (data.images && data.images.length > 0) {
                        $('#existing_images_section').removeClass('d-none');
                        $('#existing_images_count').text(data.images.length);

                        data.images.forEach(function (img) {
                            const mediaItem = `
                                <div class="existing-img-wrapper border shadow-xs" id="media_item_${img.id}">
                                    ${img.is_video 
                                        ? `<video src="${img.url}"></video>` 
                                        : `<img src="${img.url}" alt="img">`
                                    }
                                    <button type="button" class="existing-img-del-btn" data-img-id="${img.id}" title="{{ __('Delete Image') }}">
                                        <i class="icon-base bx bx-x"></i>
                                    </button>
                                </div>
                            `;
                            container.append(mediaItem);
                        });
                    } else {
                        $('#existing_images_section').addClass('d-none');
                    }

                    $('#modal_title').text('{{ __("Edit Album") }}');
                    $('#btn_save_text').text('{{ __("Update Album") }}');
                    offcanvas.show();
                },
                error: function () {
                    showToast('danger', '{{ __("Gallery not found.") }}');
                }
            });
        });

        // -------------------------------------------------------------
        // Delete Individual Image Inside Edit Offcanvas
        // -------------------------------------------------------------
        $(document).on('click', '.existing-img-del-btn', function (e) {
            e.preventDefault();
            const imgId = $(this).data('img-id');
            const itemEl = $(`#media_item_${imgId}`);

            if (!confirm('{{ __("Delete Image") }}?')) return;

            $.ajax({
                url: `${baseUrl}/images/${imgId}`,
                type: 'DELETE',
                success: function (res) {
                    if (res.success) {
                        itemEl.fadeOut(200, function () { $(this).remove(); });
                        showToast('success', res.message || '{{ __("Image deleted successfully.") }}');
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    showToast('danger', xhr.responseJSON?.message || '{{ __("Image not found.") }}');
                }
            });
        });

        // -------------------------------------------------------------
        // Submit Form (Add / Edit)
        // -------------------------------------------------------------
        $('#galleryForm').on('submit', function (e) {
            e.preventDefault();
            clearValidationErrors();

            const form = $(this);
            const id = $('#gallery_id').val();
            const isEdit = !!id;
            const url = isEdit ? `${baseUrl}/${id}` : baseUrl;

            const formData = new FormData(form[0]);
            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            const scope = $('input[name="scope_option"]:checked').val();
            if (scope === 'class') {
                formData.delete('student_id');
                formData.delete('student_ids[]');
            } else if (scope === 'single') {
                formData.delete('student_ids[]');
            } else if (scope === 'selected') {
                formData.delete('student_id');
            }

            const btn = $('#btn_save_gallery');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="icon-base bx bx-check font-18"></i> ' + $('#btn_save_text').text());
                    offcanvas.hide();
                    showToast('success', res.message || (isEdit ? '{{ __("Gallery updated successfully.") }}' : '{{ __("Gallery created successfully.") }}'));
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="icon-base bx bx-check font-18"></i> ' + $('#btn_save_text').text());
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (const key in errors) {
                            $(`#error_${key.replace('.', '_')}`).text(errors[key][0]);
                        }
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __("Failed to save.") }}';
                        showToast('danger', msg);
                    }
                }
            });
        });

        function clearValidationErrors() {
            $('[id^="error_"]').text('');
        }

        // -------------------------------------------------------------
        // Delete Gallery Action
        // -------------------------------------------------------------
        $(document).on('click', '.delete-gallery-btn', function () {
            targetDeleteId = $(this).data('id');
            deleteModal.show();
        });

        $('#btn_confirm_delete').on('click', function () {
            if (!targetDeleteId) return;
            const confirmBtn = $(this);
            confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: `${baseUrl}/${targetDeleteId}`,
                type: 'DELETE',
                success: function (res) {
                    confirmBtn.prop('disabled', false).text('{{ __("Delete") }}');
                    deleteModal.hide();
                    showToast('success', res.message || '{{ __("Gallery deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    confirmBtn.prop('disabled', false).text('{{ __("Delete") }}');
                    deleteModal.hide();
                    showToast('danger', xhr.responseJSON?.message || '{{ __("Gallery not found.") }}');
                }
            });
        });
    }

    if (document.readyState === 'complete') {
        initGalleries();
    } else {
        window.addEventListener('load', initGalleries);
    }
})();
</script>
@endsection
