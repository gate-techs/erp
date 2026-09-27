@extends('layouts/contentNavbarLayout')

@section('title', __('Roles') . ' - ' . config('variables.templateName'))

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-shield-quarter text-primary font-22"></i>
                {{ __('Roles') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Manage system roles') }}</p>
        </div>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#roleModal" id="addRoleButton">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Role') }}</span>
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-datatable table-responsive">
            <table id="rolesTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>{{ __('Role Name') }}</th>
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

<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content" id="roleForm">
            @csrf
            <span id="roleMethod"></span>
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">{{ __('Add Role') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Role Name (Arabic)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" id="roleNameAr" class="form-control" required dir="rtl" placeholder="مثال: مدير النظام / محاسب">
                </div>
                <div class="mb-3">
                    <label class="form-label required fw-semibold">{{ __('Role Name (English)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" id="roleNameEn" class="form-control" required dir="ltr" placeholder="e.g. System Administrator / Accountant">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Role') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" id="deleteRoleForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="icon-base bx bx-error-circle" style="font-size: 3rem;"></i>
                </div>
                <h5 class="modal-title mb-2">{{ __('Delete Confirmation') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete') }} 
                    <strong id="deleteRoleName" class="text-heading"></strong>؟
                </p>
                <p class="text-muted small mt-1">{{ __('This action will remove this role.') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center gap-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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

    const table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: "{{ route('settings.roles.data') }}",
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false, 
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'translated_name', 
                name: 'name', 
                render: (data, type, row) => `
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded d-inline-flex align-items-center justify-content-center">
                            <i class="icon-base bx bx-shield-quarter font-16"></i>
                        </div>
                        <div>
                            <span class="fw-semibold text-heading">${escapeHtml(row.name_ar || data || row.name)}</span>
                            ${row.name_en ? `<small class="text-muted d-block">${escapeHtml(row.name_en)}</small>` : (row.name && row.name !== row.name_ar ? `<small class="text-muted d-block">(${escapeHtml(row.name)})</small>` : '')}
                        </div>
                        ${row.is_system ? `<span class="badge bg-label-secondary font-11 py-0 px-2 ms-1"><i class="bx bx-lock-alt font-12 me-1"></i>{{ __('System') }}</span>` : ''}
                    </div>`
            },
            { 
                data: 'created_at', 
                name: 'created_at', 
                render: data => `<small class="text-muted">${data || '-'}</small>`
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                className: 'text-center', 
                render: (data, type, row) => `
                    <div class="d-inline-flex gap-1">
                        <a href="{{ url('settings/roles') }}/${row.id}/edit" class="btn btn-icon btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </a>
                        ${row.is_system 
                            ? `<button type="button" class="btn btn-icon btn-sm btn-outline-secondary" disabled title="{{ __('Protected system role cannot be deleted') }}">
                                   <i class="icon-base bx bx-lock-alt"></i>
                               </button>`
                            : `<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-role" title="{{ __('Delete') }}" data-id="${row.id}" data-name="${escapeHtml(row.translated_name || row.name)}">
                                   <i class="icon-base bx bx-trash"></i>
                               </button>`
                        }
                    </div>`
            }
        ],
        order: [[1, 'asc']]
    });

    function escapeHtml(value) { return $('<div>').text(value ?? '').html(); }
    const form = $('#roleForm');
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;

    $('#addRoleButton').on('click', function () {
        form.attr('action', "{{ route('settings.roles.store') }}");
        $('#roleMethod').html('');
        $('#roleModalTitle').text('{{ __('Add Role') }}');
        form[0].reset();
    });

    $('#rolesTable').on('click', '.btn-delete-role', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteRoleForm').attr('action', "{{ url('settings/roles') }}/" + id);
        $('#deleteRoleName').text(name);
        if (deleteModal) {
            deleteModal.show();
        } else {
            if (confirm('{{ __('Are you sure you want to delete this role?') }}')) {
                $('#deleteRoleForm').submit();
            }
        }
    });
});
</script>
@endsection
