@extends('layouts/contentNavbarLayout')

@section('title', __('Leads') . ' - ' . config('variables.templateName'))

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                <i class="icon-base bx bx-user-plus text-primary"></i>
                {{ __('Leads') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Manage your potential customers.') }}</p>
        </div>
        @can('crm.leads.create')
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddLead">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Lead') }}</span>
        </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-datatable">
            <table id="leadsTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Civil ID') }}</th>
                        <th>{{ __('Job Title') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
        tabindex="-1" id="offcanvasAddLead" style="width: 520px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-user-plus text-primary"></i>
                <span>{{ __('Add New Lead') }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="{{ route('crm.leads.store') }}" method="POST">
                @csrf
                @include('crm::leads.partials.form', ['prefix' => 'add', 'lead' => null])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base bx bx-check me-1"></i>{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
        tabindex="-1" id="offcanvasEditLead" style="width: 520px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Lead') }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editLeadForm">
                @csrf
                @method('PUT')
                @include('crm::leads.partials.form', ['prefix' => 'edit', 'lead' => null])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base bx bx-check me-1"></i>{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>

    <form id="deleteLeadForm" action="" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('page-script')
    <script>
        window.addEventListener('load', function () {
            const $ = window.jQuery || window.$;
            if (!$ || !$.fn.DataTable) return;

            const table = $('#leadsTable').DataTable({
                processing: true,
                serverSide: true,
                pagingType: 'simple_numbers',
                ajax: '{{ route('crm.leads.data') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', render: data => `<span class="fw-semibold text-heading">${data || '-'}</span>` },
                    { data: 'phone', name: 'phone' },
                    { data: 'email', name: 'email' },
                    { data: 'civil_id', name: 'civil_id' },
                    { data: 'job_title', name: 'job_title' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[6, 'desc']]
            });

            $(document).on('click', '.btn-edit-lead', function () {
                const button = $(this);
                $('#editLeadForm').attr('action', `{{ url('crm/leads') }}/${button.data('id')}`);
                $('#edit_name').val(button.data('name'));
                $('#edit_phone').val(button.data('phone'));
                $('#edit_email').val(button.data('email'));
                $('#edit_civil_id').val(button.data('civil-id'));
                $('#edit_job_title').val(button.data('job-title'));
                $('#edit_address').val(button.data('address'));
                $('#edit_notes').val(button.data('notes'));
                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasEditLead')).show();
            });

            $(document).on('click', '.btn-delete-lead', function () {
                if (!confirm('{{ __('Are you sure you want to delete this lead?') }}')) return;
                $('#deleteLeadForm').attr('action', `{{ url('crm/leads') }}/${$(this).data('id')}`).submit();
            });
        });
    </script>
@endsection
