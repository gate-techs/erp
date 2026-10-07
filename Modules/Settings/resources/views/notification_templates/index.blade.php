@extends('layouts/contentNavbarLayout')

@section('title', __('Notification Templates'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="icon-base bx bx-bell text-primary me-2"></i>{{ __('Notification Templates') }}</h4>
        <p class="text-muted mb-0 font-13">{{ __('Manage bilingual templates used by the Portal and future Web Push notifications.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @foreach($modules as $module)
            <a href="{{ route('settings.notification-templates.index', ['module' => $module]) }}" class="btn btn-sm {{ $selectedModule === $module ? 'btn-primary' : 'btn-outline-primary' }}">{{ ucfirst($module) }}</a>
        @endforeach
        @if($selectedModule !== 'all')
            <a href="{{ route('settings.notification-templates.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('All') }}</a>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Template') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php($previewColor = preg_match('/^#[0-9a-f]{6}$/i', (string) $template->color) ? $template->color : '#696cff')
                                <span class="avatar avatar-sm" style="background: {{ $previewColor }}22; color: {{ $previewColor }}"><i class="bx {{ $template->icon ?: 'bx-bell' }}"></i></span>
                                <div><strong>{{ app()->getLocale() === 'ar' ? $template->name_ar : ($template->name_en ?: $template->name_ar) }}</strong><small class="d-block text-muted">{{ $template->description }}</small></div>
                            </div>
                        </td>
                        <td><span class="badge bg-label-{{ $template->is_active ? 'success' : 'secondary' }}">{{ $template->is_active ? __('Active') : __('Inactive') }}</span></td>
                        <td class="text-end"><a href="{{ route('settings.notification-templates.edit', $template->id) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit-alt"></i> {{ __('Edit') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-5 text-muted">{{ __('No notification templates found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.querySelectorAll('.template-toggle').forEach(function (el) {
    el.addEventListener('change', function () {});
});
</script>
@endsection
