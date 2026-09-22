@extends('layouts/contentNavbarLayout')

@section('title', __('Print Settings') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
.template-card {
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(0,0,0,0.08);
}
.template-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -4px rgba(0,0,0,0.12) !important;
    border-color: #696cff;
}
.template-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.sheet-mini-preview {
    background: #f8f9fa;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 12px;
    height: 120px;
    position: relative;
    overflow: hidden;
}
.sheet-mini-preview .mini-line {
    background: #e2e8f0;
    height: 6px;
    border-radius: 3px;
    margin-bottom: 6px;
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-printer text-primary font-24"></i>
            <span>{{ __('Print Settings') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">{{ __('System Settings') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Print Settings') }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Intro Banner -->
<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
    <div class="card-body p-4 position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h5 class="text-white fw-bold mb-2 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-layer font-22 text-warning"></i>
                    <span>{{ __('Dynamic Print Templates Engine') }}</span>
                </h5>
                <p class="text-white-50 mb-0 font-14">
                    {{ __('Manage and customize official print templates across the ERP system. Control headers, logos, margins, footers, QR verification, and signature approvals from a single unified hub.') }}
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
                <span class="badge bg-white bg-opacity-10 text-white font-13 px-3 py-2 border border-light border-opacity-25">
                    <i class="icon-base bx bx-check-double me-1 text-success"></i>
                    {{ $templates->where('is_active', true)->count() }} {{ __('Active Templates') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Templates Cards Grid -->
<div class="row g-4">
    @foreach($templates as $tpl)
        @php
            $typeColors = [
                'report' => ['bg' => 'bg-label-primary', 'text' => 'text-primary', 'icon' => 'bx-bar-chart-alt-2'],
                'financial' => ['bg' => 'bg-label-success', 'text' => 'text-success', 'icon' => 'bx-dollar-circle'],
                'letter' => ['bg' => 'bg-label-info', 'text' => 'text-info', 'icon' => 'bx-certification'],
            ];
            $style = $typeColors[$tpl->type] ?? $typeColors['report'];
        @endphp
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card h-100 template-card shadow-sm border-0 position-relative">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Card Header -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="template-icon-wrap {{ $style['bg'] }}">
                                <i class="icon-base bx {{ $style['icon'] }} font-24 {{ $style['text'] }}"></i>
                            </div>
                            <div class="form-check form-switch mb-0" title="{{ __('Toggle Template Status') }}">
                                <input class="form-check-input toggle-template-status" type="checkbox" data-id="{{ $tpl->id }}" {{ $tpl->is_active ? 'checked' : '' }}>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <h5 class="fw-bold mb-1 text-heading">{{ $tpl->display_name }}</h5>
                        <p class="text-muted font-13 mb-3" style="min-height: 40px;">
                            {{ $tpl->description ?: __('Standard template configuration.') }}
                        </p>

                        <!-- Mini Sheet Preview Graphic -->
                        <div class="sheet-mini-preview mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="mini-line" style="width: 35%; height: 8px; background: #696cff;"></div>
                                <div class="mini-line" style="width: 20%; height: 8px;"></div>
                            </div>
                            <div class="mini-line" style="width: 80%;"></div>
                            <div class="mini-line" style="width: 95%;"></div>
                            <div class="mini-line" style="width: 90%;"></div>
                            <div class="d-flex justify-content-between mt-3">
                                <div class="mini-line" style="width: 25%;"></div>
                                <div class="mini-line" style="width: 25%;"></div>
                            </div>
                        </div>

                        <!-- Meta Chips -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge bg-label-secondary font-11">
                                <i class="icon-base bx bx-file-blank me-1"></i>{{ $tpl->page_size }}
                            </span>
                            <span class="badge bg-label-secondary font-11">
                                <i class="icon-base bx {{ $tpl->orientation === 'landscape' ? 'bx-dock-left' : 'bx-dock-top' }} me-1"></i>
                                {{ $tpl->orientation === 'landscape' ? __('Landscape') : __('Portrait') }}
                            </span>
                            @if($tpl->is_default)
                                <span class="badge bg-label-warning font-11">
                                    <i class="icon-base bx bx-star me-1"></i>{{ __('Default Template') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('settings.print-templates.preview', $tpl->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                            <i class="icon-base bx bx-show font-16"></i>
                            <span>{{ __('Print Preview') }}</span>
                        </a>
                        <a href="{{ route('settings.print-templates.edit', $tpl->id) }}" class="btn btn-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                            <i class="icon-base bx bx-slider font-16"></i>
                            <span>{{ __('Customize') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-template-status').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const id = this.dataset.id;
            const cb = this;

            fetch(`{{ url('settings/print-templates') }}/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(function (res) {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(function (data) {
                if (window.notify) {
                    window.notify.success(data.message);
                }
            })
            .catch(function () {
                cb.checked = !cb.checked;
                if (window.notify) {
                    window.notify.error('{{ __('Failed to update status') }}');
                }
            });
        });
    });
});
</script>
@endsection
