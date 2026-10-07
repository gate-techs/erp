@extends('layouts/contentNavbarLayout')
@section('title', __('General Notifications'))
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4"><div><h4 class="fw-bold mb-1"><i class="bx bx-broadcast text-primary me-2"></i>{{ __('General Notifications') }}</h4><p class="text-muted mb-0">{{ __('Manage messages sent to users.') }}</p></div><a href="{{ route('notifications.broadcasts.create') }}" class="btn btn-primary btn-sm"><i class="bx bx-plus me-1"></i>{{ __('New General Notification') }}</a></div>
<div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table id="broadcastsTable" class="table table-hover align-middle w-100"><thead><tr><th>{{ __('Message') }}</th><th>{{ __('Audience') }}</th><th>{{ __('Recipients') }}</th><th>{{ __('Status') }}</th><th>{{ __('Scheduled At') }}</th><th>{{ __('Created At') }}</th></tr></thead></table></div></div></div>
@endsection
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#broadcastsTable').DataTable({ processing: true, serverSide: true, ajax: '{{ route('notifications.broadcasts.data') }}', order: [[5, 'desc']], columns: [
        { data: 'title', name: 'title', render: data => `<strong>${data}</strong>` }, { data: 'audience', name: 'audience' }, { data: 'recipients', name: 'recipients' }, { data: 'status', name: 'status' }, { data: 'scheduled_at', name: 'scheduled_at' }, { data: 'created_at', name: 'created_at' }
    ], language: { search: '{{ __('Search:') }}', emptyTable: '{{ __('No general notifications have been sent yet.') }}', processing: '{{ __('Loading...') }}' } });
});
</script>
@endsection
