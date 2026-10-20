@extends('layouts/contentNavbarLayout')

@section('title', __('Cash & Bank Accounts') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-wallet text-primary font-24"></i>
            <span>{{ __('Cash & Bank Accounts') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Cash & Bank Accounts') }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('accounting.cash-accounts.create')
        <a href="{{ route('accounting.cash-accounts.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i><span>{{ __('Add New Account') }}</span>
        </a>
        @endcan
    </div>
</div>

<div id="alertContainer">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-check-circle me-2 font-20"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-error-circle me-2 font-20"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<!-- Filters Card (2-row toolbar) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Accounting Entity') }}</label>
                <select id="filter_entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Entities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Entities') }}</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">{{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Account Type') }}</label>
                <select id="filter_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_is_active" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height:38px">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cash & Bank Accounts DataTable -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="cashAccountsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width:50px">#</th>
                    <th style="min-width:380px">{{ __('Account Name & Code') }}</th>
                    <th>{{ __('Accounting Entity') }}</th>
                    <th class="text-center">{{ __('Account Type') }}</th>
                    <th>{{ __('GL Account') }}</th>
                    <th class="text-center">{{ __('Currency') }}</th>
                    <th>{{ __('Responsible Person') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width:130px">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCashAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteCashAccountForm" class="modal-content">
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
                <button type="submit" class="btn btn-danger" id="btnConfirmDelete">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.cash-accounts.data') }}";
    const baseUrl = "{{ url('accounting/cash-accounts') }}";

    function alertBox(type, message) {
        $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert">
                <i class="icon-base bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    const table = $('#cashAccountsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.type = $('#filter_type').val();
                d.is_active = $('#filter_is_active').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'account_info', name: 'account_info', className: 'align-middle' },
            { data: 'entity_name', name: 'cash_accounts.entity_id', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'type_badge', name: 'cash_accounts.type', className: 'text-center align-middle' },
            { data: 'gl_account', name: 'account_id', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'currency_display', name: 'currency', className: 'text-center align-middle' },
            { data: 'responsible_person', name: 'responsible_user_id', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'status_badge', name: 'is_active', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'asc']],
        language: {
            search: '',
            searchPlaceholder: "{{ __('Search...') }}",
            paginate: {
                previous: '<i class="bx bx-chevron-left"></i>',
                next: '<i class="bx bx-chevron-right"></i>'
            }
        }
    });

    $('#filter_entity_id, #filter_type, #filter_is_active').on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id, #filter_type, #filter_is_active').val('').trigger('change');
        table.ajax.reload();
    });

    // Toggle Status
    $(document).on('click', '.btn-toggle-status', function () {
        const id = $(this).data('id');

        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                table.ajax.reload();
                alertBox('success', res.message || "{{ __('Status updated successfully.') }}");
            },
            error: function () {
                alertBox('danger', "{{ __('Failed to update status.') }}");
            }
        });
    });

    // Delete Modal
    const deleteModalEl = document.getElementById('deleteCashAccountModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;
    const deleteForm = $('#deleteCashAccountForm');

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.attr('action', baseUrl + '/' + id);
        $('#delete_target_name').text(name);

        if (deleteModal) deleteModal.show();
    });

    deleteForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnConfirmDelete');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: deleteForm.attr('action'),
            method: 'POST',
            data: deleteForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload();
                alertBox('success', res.message || "{{ __('Cash Account deleted successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('Failed to delete cash account.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
