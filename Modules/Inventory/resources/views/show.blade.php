@extends('layouts/contentNavbarLayout')

@section('title', __('Warehouse Details') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-package text-primary font-24"></i>
            <span>{{ __('Warehouse Details') }}: {{ $warehouse->translate(app()->getLocale())?->name ?? $warehouse->translate('ar')?->name ?? $warehouse->code }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('Warehouses') }}</a></li>
                <li class="breadcrumb-item active">{{ $warehouse->code }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm">
        <i class="icon-base bx bx-arrow-back font-18"></i>
        <span>{{ __('Back') }}</span>
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar avatar-md bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="icon-base bx {{ $warehouse->isMain() ? 'bx-home' : 'bx-git-branch' }} font-22"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $warehouse->translate(app()->getLocale())?->name ?? $warehouse->translate('ar')?->name ?? $warehouse->code }}</h5>
                        <small class="text-muted" dir="ltr">{{ $warehouse->code }}</small>
                    </div>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">{{ __('Warehouse Type') }}</span>
                        <span class="badge bg-label-{{ $warehouse->isMain() ? 'primary' : 'info' }}">
                            <i class="icon-base bx {{ $warehouse->isMain() ? 'bx-home' : 'bx-git-branch' }} me-1"></i>
                            {{ $warehouse->isMain() ? __('Main Warehouse') : __('Branch Warehouse') }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">{{ __('Status') }}</span>
                        <span class="badge bg-label-{{ $warehouse->status === 'active' ? 'success' : 'danger' }}">
                            {{ $warehouse->status === 'active' ? __('Active') : __('Inactive') }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">{{ __('Accounting Entity') }}</span>
                        <span class="fw-semibold">{{ $warehouse->entity?->translate(app()->getLocale())?->name ?? $warehouse->entity?->translate('ar')?->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">{{ __('Branch') }}</span>
                        <span class="fw-semibold">
                            @if($warehouse->isMain())
                                <span class="badge bg-label-primary">{{ __('Main / General') }}</span>
                            @else
                                {{ $warehouse->branch?->translate(app()->getLocale())?->name ?? $warehouse->branch?->translate('ar')?->name ?? '-' }}
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">{{ __('Created Date') }}</span>
                        <span class="fw-semibold">{{ $warehouse->created_at?->format('d-m-Y H:i') ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-bottom py-3">
                <h6 class="mb-0 fw-bold">{{ __('Translations & Details') }}</h6>
            </div>
            <div class="card-body py-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-2 border">
                            <h6 class="fw-bold text-primary mb-2">{{ __('Arabic') }}</h6>
                            <p class="mb-1"><strong>{{ __('Name') }}:</strong> {{ $warehouse->translate('ar')?->name ?? '-' }}</p>
                            <p class="mb-0 text-muted"><strong>{{ __('Description') }}:</strong> {{ $warehouse->translate('ar')?->description ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-2 border">
                            <h6 class="fw-bold text-primary mb-2">{{ __('English') }}</h6>
                            <p class="mb-1" dir="ltr"><strong>{{ __('Name') }}:</strong> {{ $warehouse->translate('en')?->name ?? '-' }}</p>
                            <p class="mb-0 text-muted" dir="ltr"><strong>{{ __('Description') }}:</strong> {{ $warehouse->translate('en')?->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
