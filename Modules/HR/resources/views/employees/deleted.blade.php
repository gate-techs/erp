@extends('layouts/contentNavbarLayout')

@section('title', __('Deleted Employees') . ' - ' . config('variables.templateName'))

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hr.employees.index') }}" class="text-muted fw-medium small">{{ __('Employees') }}</a></li>
                    <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Deleted Employees') }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 mt-1">
                <i class="icon-base bx bx-trash text-secondary font-24"></i>
                <h4 class="mb-0 fw-bold text-heading">{{ __('Deleted Employees') }}</h4>
            </div>
        </div>

        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 mt-3 mt-md-0">
            <i class="icon-base bx bx-arrow-back"></i>
            <span>{{ __('Back to Employees') }}</span>
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-check-circle me-2"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('hr.employees.deleted') }}" class="row g-2">
                <div class="col-md-5">
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('Search by name or employee code') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-search"></i>
                        <span>{{ __('Search') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Employee Code') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Deleted At') }}</th>
                        <th class="text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        @php
                            $employeeName = $employee->translate(app()->getLocale())?->name
                                ?? $employee->translate('ar')?->name
                                ?? $employee->translate('en')?->name
                                ?? __('Unnamed Employee');
                            $departmentName = $employee->department?->translate(app()->getLocale())?->name
                                ?? $employee->department?->translate('ar')?->name
                                ?? $employee->department?->translate('en')?->name
                                ?? '-';
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $employeeName }}</td>
                            <td><span class="badge bg-label-primary">{{ $employee->employee_code }}</span></td>
                            <td>{{ $departmentName }}</td>
                            <td>{{ $employee->deleted_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="text-center">
                                @can('hr.employees.edit')
                                <form method="POST" action="{{ route('hr.employees.restore', $employee->id) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-undo"></i>
                                        <span>{{ __('Restore') }}</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">-</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">{{ __('No deleted employees found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="card-footer bg-transparent">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
@endsection
