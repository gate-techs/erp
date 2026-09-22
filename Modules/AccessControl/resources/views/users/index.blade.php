@extends('layouts/contentNavbarLayout')

@section('title', __('Users') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-user-check text-primary font-24"></i>
            <span>{{ __('Users') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('User Management') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Users') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <!-- Button that opens the Side Offcanvas matching the theme design -->
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" aria-controls="offcanvasAddUser">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New User') }}</span>
        </button>
    </div>
</div>

<!-- Standard Filters Card (Exact Match with /hr/leaves & employees layout) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Role Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Role') }}</label>
                <select id="filter_role" class="select2 form-select" data-placeholder="{{ __('All Roles') }}" data-allow-clear="true">
                    <option value="">{{ __('All Roles') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ __($role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. User Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('User Type') }}</label>
                <select id="filter_user_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($userTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- 4. Reset Filters Action Button (Exact Match with /hr/leaves) -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Users DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="usersTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Username') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('User Type') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New User (Exact Match with /hr/leaves side offcanvas)    -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-user-plus text-primary font-22"></i>
            <span>{{ __('Add New User') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addUserForm" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Image Upload Box with Camera Icon matching system style -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Profile Photo') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="image-upload-wrapper border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('add_user_avatar').click();">
                    
                    <div id="add_avatar_placeholder" class="d-flex align-items-center justify-content-between w-100 px-2">
                        <span class="text-muted small">{{ __('Add New Image') }}</span>
                        <i class="icon-base bx bx-camera text-primary font-24"></i>
                    </div>

                    <div id="add_avatar_preview_box" class="d-none align-items-center gap-2 w-100">
                        <img id="add_avatar_preview" src="" alt="Preview" class="rounded-circle object-fit-cover border shadow-xs flex-shrink-0" width="56" height="56">
                        <div class="text-start">
                            <span id="add_avatar_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="add_user_avatar" name="avatar" class="d-none" accept="image/*" onchange="previewAddAvatar(this)">
                </div>
            </div>

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="{{ __('e.g. أحمد محمد / John Doe') }}">
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Username') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-at"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="amohamed" dir="ltr">
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Email') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                    <input type="email" name="email" class="form-control" required placeholder="user@example.com" dir="ltr">
                </div>
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Phone') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                    <input type="text" name="phone" class="form-control" placeholder="05xxxxxxxx" dir="ltr">
                </div>
            </div>

            <!-- User Type (Select2 exactly like /hr/leaves) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="add_user_type">{{ __('User Type') }} <span class="text-danger">*</span></label>
                <select id="add_user_type" name="user_type" class="form-select select2" data-placeholder="{{ __('Select User Type...') }}" required>
                    <option value="">{{ __('Select User Type...') }}</option>
                    @foreach ($userTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role (Select2 exactly like /hr/leaves) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="add_user_role">{{ __('Role') }} <span class="text-danger">*</span></label>
                <select id="add_user_role" name="role" class="form-select select2" data-placeholder="{{ __('Select Role...') }}" required>
                    <option value="">{{ __('Select Role...') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ __($role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Password') }} <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                    <input type="password" name="password" id="add_user_password" class="form-control" required placeholder="············">
                    <span class="input-group-text cursor-pointer toggle-password" data-target="#add_user_password"><i class="bx bx-hide"></i></span>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading mb-2">{{ __('Active Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="statusActiveAdd" value="1" checked>
                        <label class="form-check-label fw-semibold text-success" for="statusActiveAdd">{{ __('Active') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="statusInactiveAdd" value="0">
                        <label class="form-check-label fw-semibold text-muted" for="statusInactiveAdd">{{ __('Inactive') }}</label>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save User') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit User (Exact Match with /hr/leaves side offcanvas)       -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditUserLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit User') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editUserForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Image Upload Box -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Profile Photo') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('edit_user_avatar').click();">
                    
                    <div id="edit_avatar_preview_box" class="d-flex align-items-center gap-2 w-100">
                        <img id="edit_avatar_preview" src="" alt="Preview" class="rounded-circle object-fit-cover border shadow-xs flex-shrink-0" width="56" height="56">
                        <div class="text-start">
                            <span id="edit_avatar_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="edit_user_avatar" name="avatar" class="d-none" accept="image/*" onchange="previewEditAvatar(this)">
                </div>
            </div>

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="edit_user_name" class="form-control" required placeholder="{{ __('e.g. أحمد محمد / John Doe') }}">
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Username') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-at"></i></span>
                    <input type="text" name="username" id="edit_user_username" class="form-control" placeholder="amohamed" dir="ltr">
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Email') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                    <input type="email" name="email" id="edit_user_email" class="form-control" required placeholder="user@example.com" dir="ltr">
                </div>
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Phone') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                    <input type="text" name="phone" id="edit_user_phone" class="form-control" placeholder="05xxxxxxxx" dir="ltr">
                </div>
            </div>

            <!-- User Type (Select2) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="edit_user_type">{{ __('User Type') }} <span class="text-danger">*</span></label>
                <select name="user_type" id="edit_user_type" class="form-select select2" data-placeholder="{{ __('Select User Type...') }}" required>
                    <option value="" disabled>{{ __('Select User Type...') }}</option>
                    @foreach ($userTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role (Select2) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="edit_user_role">{{ __('Role') }} <span class="text-danger">*</span></label>
                <select name="role" id="edit_user_role" class="form-select select2" data-placeholder="{{ __('Select Role...') }}" required>
                    <option value="" disabled>{{ __('Select Role...') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ __($role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Password') }}</label>
                <div class="input-group input-group-merge">
                    <input type="password" name="password" id="edit_user_password" class="form-control" placeholder="············">
                    <span class="input-group-text cursor-pointer toggle-password" data-target="#edit_user_password"><i class="bx bx-hide"></i></span>
                </div>
                <small class="text-muted">{{ __('Leave blank to keep current password') }}</small>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading mb-2">{{ __('Active Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="statusActiveEdit" value="1">
                        <label class="form-check-label fw-semibold text-success" for="statusActiveEdit">{{ __('Active') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="statusInactiveEdit" value="0">
                        <label class="form-check-label fw-semibold text-muted" for="statusInactiveEdit">{{ __('Inactive') }}</label>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete User Confirmation                                        -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteUserForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete:') }}
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
// Image preview functions
function previewAddAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('add_avatar_preview').src = e.target.result;
            document.getElementById('add_avatar_name').textContent = file.name;
            document.getElementById('add_avatar_placeholder').classList.add('d-none');
            document.getElementById('add_avatar_preview_box').classList.remove('d-none');
            document.getElementById('add_avatar_preview_box').classList.add('d-flex');
        };
        reader.readAsDataURL(file);
    }
}

function previewEditAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('edit_avatar_preview').src = e.target.result;
            document.getElementById('edit_avatar_name').textContent = file.name;
        };
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('users.data') }}";
    const baseUrl = "{{ url('users') }}";

    // 1. Initialize Server-Side DataTable (Exact match with /hr/leaves layout)
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.role = $('#filter_role').val();
                d.user_type = $('#filter_user_type').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false, 
                searchable: false, 
                className: 'text-center align-middle',
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'user_info', 
                name: 'name', 
                className: 'align-middle',
                render: (data, type, row) => data 
            },
            { 
                data: 'username_formatted', 
                name: 'username',
                className: 'align-middle',
                render: (data, type, row) => data 
            },
            { 
                data: 'phone_formatted', 
                name: 'phone',
                className: 'align-middle',
                render: (data, type, row) => data 
            },
            { 
                data: 'user_type_badge', 
                name: 'user_type', 
                className: 'text-center align-middle',
                render: (data, type, row) => data 
            },
            { 
                data: 'roles_badges', 
                name: 'roles.name',
                orderable: false, 
                className: 'align-middle',
                render: (data, type, row) => data 
            },
            { 
                data: 'status', 
                name: 'is_active', 
                className: 'text-center align-middle',
                orderable: false,
                render: (data, type, row) => data
            },
            { 
                data: 'created_at', 
                name: 'created_at', 
                className: 'text-center align-middle',
                render: data => `<span class="text-muted">${data || '-'}</span>`
            },
            { 
                data: 'actions', 
                orderable: false, 
                searchable: false, 
                className: 'text-center align-middle', 
                render: (data, type, row) => data 
            }
        ],
        order: [[1, 'asc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#usersTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload with debounce / reset guard (Exact match with /hr/leaves)
    let isResetting = false;

    $('#filter_role, #filter_user_type, #filter_status').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Ensure global select2 initializer is executed if present
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    }

    // 3. Reset Filters Handler (Exact match with /hr/leaves)
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_role, #filter_user_type, #filter_status').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 4. Toggle password show/hide
    $('.toggle-password').on('click', function () {
        const target = $($(this).data('target'));
        const icon = $(this).find('i');
        if (target.attr('type') === 'password') {
            target.attr('type', 'text');
            icon.removeClass('bx-hide').addClass('bx-show');
        } else {
            target.attr('type', 'password');
            icon.removeClass('bx-show').addClass('bx-hide');
        }
    });

    // 5. Handle Edit Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditUser');

    $(document).on('click', '.btn-edit-user', function () {
        const id = $(this).data('id');
        const form = document.getElementById('editUserForm');
        form.action = baseUrl + '/' + id;

        $('#edit_user_name').val($(this).data('name') || '');
        $('#edit_user_username').val($(this).data('username') || '');
        $('#edit_user_email').val($(this).data('email') || '');
        $('#edit_user_phone').val($(this).data('phone') || '');
        $('#edit_user_password').val('');

        const userType = $(this).data('user-type') || '';
        $('#edit_user_type').val(userType).trigger('change');

        const role = $(this).data('role') || '';
        $('#edit_user_role').val(role).trigger('change');

        const status = $(this).data('status') || 'active';
        if (status === 'active') {
            document.getElementById('statusActiveEdit').checked = true;
        } else {
            document.getElementById('statusInactiveEdit').checked = true;
        }

        const avatar = $(this).data('avatar');
        const previewImg = document.getElementById('edit_avatar_preview');
        const previewName = document.getElementById('edit_avatar_name');
        if (avatar) {
            previewImg.src = avatar;
            previewName.textContent = '{{ __("Current Image") }}';
        } else {
            previewImg.src = '{{ asset("assets/img/avatars/1.png") }}';
            previewName.textContent = '{{ __("No Image Uploaded") }}';
        }

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // 6. Handle Delete Modal
    const deleteModalEl = document.getElementById('deleteUserModal');
    const deleteForm = document.getElementById('deleteUserForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-user', function () {
        const id = $(this).data('id');
        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = $(this).data('name') || '';
        if (deleteModalEl) {
            const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
            deleteModal.show();
        }
    });

    // 7. Handle Status Toggle via AJAX
    $(document).on('click', '.btn-toggle-status', function () {
        const btn = $(this);
        const id = btn.data('id');
        const token = $('meta[name="csrf-token"]').attr('content');

        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (res) {
                if (res.success) {
                    btn.removeClass('bg-label-success bg-label-danger').addClass(res.badge);
                    btn.text(res.status === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '{{ __("Failed to update status") }}';
                alert(msg);
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
