@extends('layouts/contentNavbarLayout')

@section('title', __('Notifications List'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div><h4 class="fw-bold mb-1"><i class="bx bx-bell text-primary me-2"></i>{{ __('Notifications List') }}</h4><p class="text-muted mb-0">{{ __('Your notifications and updates.') }}</p></div>
    <a href="{{ route('notifications.broadcasts') }}" class="btn btn-outline-primary btn-sm"><i class="bx bx-broadcast me-1"></i>{{ __('General Notifications') }}</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            @php($data = is_array($notification->data) ? $notification->data : [])
            @php($locale = app()->getLocale())
            <div class="list-group-item px-4 py-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex gap-3 align-items-start">
                    <span class="avatar avatar-sm bg-label-primary"><i class="bx {{ $data['icon'] ?? 'bx-bell' }}"></i></span>
                    <div class="flex-grow-1"><div class="fw-semibold">{{ $data[$locale === 'ar' ? 'title_ar' : 'title_en'] ?? ($data['title'] ?? class_basename($notification->type)) }}</div><div class="text-muted small mt-1">{{ $data[$locale === 'ar' ? 'message_ar' : 'message_en'] ?? ($data['message'] ?? ($data['body'] ?? '')) }}</div><small class="text-muted">{{ $notification->created_at?->diffForHumans() }}</small></div>
                    @if(!$notification->read_at)<span class="badge bg-primary">{{ __('New') }}</span>@endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5"><i class="bx bx-bell-off font-32 d-block mb-2"></i>{{ __('No notifications yet.') }}</div>
        @endforelse
    </div>
    @if($notifications->hasPages())<div class="card-footer">{{ $notifications->links() }}</div>@endif
</div>
@endsection
