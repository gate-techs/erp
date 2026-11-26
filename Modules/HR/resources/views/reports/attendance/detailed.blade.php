@extends('layouts/contentNavbarLayout')

@section('title', __('Detailed Attendance Report'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
        <div>
            <h4 class="report-page-title fw-bold py-1 mb-0">
                <span class="text-muted fw-light">{{ __('HR') }} / {{ __('Reports') }} /</span>
                {{ __('Detailed Attendance Report') }}
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('hr.attendances.print', request()->all()) }}" target="_blank" class="btn btn-primary">
                <i class="bx bx-printer me-1"></i> {{ __('Print') }}
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('hr.reports.attendance.detailed') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">{{ __('Branch') }}</label>
                    <select name="branch_id" class="form-select">
                        <option value="">{{ __('All Branches') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">{{ __('Department') }}</label>
                    <select name="department_id" class="form-select">
                        <option value="">{{ __('All Departments') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">{{ __('Employee') }}</label>
                    <select name="employee_id" class="form-select">
                        <option value="">{{ __('All Employees') }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">{{ __('Month') }}</label>
                    <select name="month" class="form-select">
                        @php $selectedMonth = (int) request('month', now()->month); @endphp
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label small fw-semibold">{{ __('Year') }}</label>
                    <select name="year" class="form-select">
                        @php $selectedYear = (int) request('year', now()->year); @endphp
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .attendance-matrix-scroll-box {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: #fff;
        }
        table.matrix-attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 12px;
        }
        table.matrix-attendance-table th,
        table.matrix-attendance-table td {
            border-bottom: 1px solid #e2e8f0;
            border-inline-end: 1px solid #f1f5f9;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }
        table.matrix-attendance-table thead th {
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
        }
        table.matrix-attendance-table th.col-sticky-emp,
        table.matrix-attendance-table td.col-sticky-emp {
            position: sticky;
            inset-inline-start: 0;
            z-index: 2;
            background: #fff;
            min-width: 240px;
            max-width: 260px;
            text-align: start;
            padding: 8px 12px;
            border-inline-end: 2px solid #cbd5e1;
        }
        table.matrix-attendance-table thead th.col-sticky-emp {
            z-index: 3;
            background: #f8fafc;
        }
        table.matrix-attendance-table th.col-matrix-day,
        table.matrix-attendance-table td.col-matrix-day {
            min-width: 68px;
        }
        table.matrix-attendance-table th.today-highlight {
            background: #fef3c7 !important;
            color: #92400e !important;
        }
        .emp-card-layout {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .emp-avatar-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #334155;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 12px;
        }
        .emp-card-name {
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .emp-card-sub {
            font-size: 11px;
            color: #64748b;
        }
        .sym-present-check { color: #16a34a; font-weight: 700; font-size: 14px; }
        .sym-absent-cross { color: #dc2626; font-weight: 700; font-size: 13px; }
        .sym-restday-box { color: #94a3b8; font-size: 10px; line-height: 1.1; }
        .sym-future-dash { color: #cbd5e1; }
        .cell-times {
            display: flex;
            flex-direction: column;
            gap: 1px;
            margin-top: 2px;
            font-size: 10px;
            line-height: 1.15;
            white-space: nowrap;
        }
        .cell-time-in { color: #15803d; font-weight: 600; }
        .cell-time-out { color: #1d4ed8; font-weight: 600; }
    </style>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="m-0 fw-bold text-success d-flex align-items-center gap-2">
                <i class="bx bx-calendar"></i>
                {{ __('Attendance Report') }} : {{ $matrixData['month_name'] }} {{ $matrixData['year'] }}
            </h6>
            <div class="d-flex flex-wrap align-items-center gap-3 small text-muted">
                <span><span class="sym-present-check">✓</span> {{ __('Present') }}</span>
                <span><span class="sym-absent-cross">✕</span> {{ __('Absent') }}</span>
                <span>½ {{ __('Half Day') }}</span>
                <span>🚩 {{ __('On Leave') }}</span>
                <span>⭐ {{ __('Holiday') }}</span>
                <span>⃠ {{ __('Rest Day') }}</span>
                <span>🕒 {{ __('Late') }}</span>
            </div>
        </div>

        <div class="card-body p-2">
            <div class="attendance-matrix-scroll-box">
                <table class="matrix-attendance-table">
                    <thead>
                        <tr>
                            <th class="col-sticky-emp">{{ __('Employee') }}</th>
                            @foreach ($matrixData['days'] as $d)
                                <th class="col-matrix-day {{ $d['is_today'] ? 'today-highlight' : '' }}">
                                    {{ $d['day'] }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="col-sticky-emp" style="font-size: 11px; color: #64748b; font-weight: 500;">
                                {{ __('Attendance / Absence / Department') }}
                            </th>
                            @foreach ($matrixData['days'] as $d)
                                <th class="col-matrix-day {{ $d['is_today'] ? 'today-highlight' : '' }}">
                                    {{ app()->getLocale() === 'ar' ? $d['day_name_ar'] : $d['day_name_en'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($matrixData['matrix_rows'] as $row)
                            @php
                                $presentCount = 0;
                                $absentCount = 0;
                                foreach ($row['days'] as $dayCell) {
                                    $st = $dayCell['status'] ?? 'future';
                                    if (in_array($st, ['present', 'late', 'half_day'], true)) {
                                        $presentCount++;
                                    } elseif ($st === 'absent') {
                                        $absentCount++;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="col-sticky-emp">
                                    <div class="emp-card-layout">
                                        <div class="emp-avatar-badge">
                                            @if ($row['avatar'])
                                                <img src="{{ asset('storage/' . $row['avatar']) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            @else
                                                {{ strtoupper(mb_substr($row['employee_name'], 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="emp-card-info" style="overflow: hidden; flex: 1;">
                                            <div class="d-flex align-items-center justify-content-between gap-1">
                                                <div class="emp-card-name" title="{{ $row['employee_name'] }}">
                                                    {{ $row['employee_name'] }}
                                                </div>
                                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                    <span class="badge bg-label-success" style="font-size: 10px; padding: 2px 5px;" title="{{ __('Present Days') }}">{{ __('P') }}: {{ $presentCount }}</span>
                                                    <span class="badge bg-label-danger" style="font-size: 10px; padding: 2px 5px;" title="{{ __('Absent Days') }}">{{ __('A') }}: {{ $absentCount }}</span>
                                                </div>
                                            </div>
                                            <div class="emp-card-sub">
                                                {{ $row['employee_code'] }} • {{ $row['department'] ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @foreach ($matrixData['days'] as $d)
                                    @php
                                        $cell = $row['days'][$d['day']] ?? null;
                                        $status = $cell['status'] ?? 'future';
                                        $isToday = $d['is_today'];
                                        $checkIn = $cell['check_in'] ?? null;
                                        $checkOut = $cell['check_out'] ?? null;
                                    @endphp
                                    <td class="col-matrix-day {{ $isToday ? 'bg-warning-subtle' : '' }}">
                                        @if ($status === 'present')
                                            <span class="sym-present-check">✓</span>
                                        @elseif ($status === 'absent')
                                            <span class="sym-absent-cross">✕</span>
                                        @elseif ($status === 'rest_day')
                                            <div class="sym-restday-box">
                                                <span class="ico">⃠</span>
                                                <span class="txt d-block">{{ __('Rest Day') }}</span>
                                            </div>
                                        @elseif ($status === 'holiday')
                                            <span class="sym-holiday-star">⭐</span>
                                        @elseif ($status === 'late')
                                            <span class="sym-late-clock">🕒</span>
                                        @elseif ($status === 'half_day')
                                            <span class="sym-half-day">½</span>
                                        @elseif ($status === 'on_leave')
                                            <span class="sym-leave-flag">🚩</span>
                                        @else
                                            <span class="sym-future-dash">-</span>
                                        @endif

                                        @if ($checkIn || $checkOut)
                                            <div class="cell-times">
                                                @if ($checkIn)
                                                    <span class="cell-time-in" title="{{ __('Check In') }}">{{ $checkIn }}</span>
                                                @endif
                                                @if ($checkOut)
                                                    <span class="cell-time-out" title="{{ __('Check Out') }}">{{ $checkOut }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($matrixData['days']) + 1 }}" class="text-center py-5 text-muted">
                                    <i class="bx bx-user-x fs-1 mb-2 text-secondary d-block"></i>
                                    <p class="mb-0">{{ __('No records found matching the selected filters.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
