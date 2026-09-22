<div>
    <!-- Header Meta Summary -->
    <div class="d-flex align-items-center justify-content-between p-3 bg-lighter rounded mb-3">
        <div>
            <span class="badge {{ $log->event_badge_color }} me-1">{{ $log->event_label }}</span>
            <strong class="text-dark">{{ $log->subject_type_label }}:</strong>
            <span class="text-primary fw-semibold">{{ $log->subject_title }}</span>
        </div>
        <div class="text-muted small">
            <i class="bx bx-time-five me-1"></i> {{ $log->created_at?->format('Y-m-d H:i:s') }}
        </div>
    </div>

    <!-- Description & User Info -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="p-2 border rounded bg-white">
                <small class="text-muted d-block">{{ __('المستخدم المنفذ') }}</small>
                <strong>{{ $log->causer?->name ?? __('نظام / تلقائي') }}</strong>
                @if($log->causer?->email)
                    <small class="text-muted d-block">({{ $log->causer->email }})</small>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-2 border rounded bg-white">
                <small class="text-muted d-block">{{ __('معلومات الاتصال') }}</small>
                <span class="badge bg-label-secondary font-monospace">{{ $log->properties['ip'] ?? request()->ip() }}</span>
                <span class="text-muted small d-block text-truncate" title="{{ $log->properties['user_agent'] ?? '-' }}">
                    {{ $log->properties['user_agent'] ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Field Differences (Before vs After) Table -->
    <h6 class="fw-bold mb-2 text-dark d-flex align-items-center gap-1">
        <i class="bx bx-git-compare text-primary"></i> {{ __('تفاصيل التغييرات والحقول (Before & After)') }}
    </h6>

    @if(!empty($diffs) && count($diffs) > 0)
        <div class="table-responsive border rounded">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">{{ __('الحقل') }}</th>
                        <th style="width: 37%;" class="text-danger bg-label-danger bg-opacity-10">{{ __('القيمة السابقة (Before)') }}</th>
                        <th style="width: 38%;" class="text-success bg-label-success bg-opacity-10">{{ __('القيمة الجديدة (After)') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($diffs as $d)
                        <tr>
                            <td>
                                <strong>{{ $d['field_label'] }}</strong>
                                <small class="text-muted d-block font-monospace">({{ $d['field'] }})</small>
                            </td>
                            <td class="text-danger" style="background-color: #fef2f2; word-break: break-all;">
                                <del>{{ $d['old'] }}</del>
                            </td>
                            <td class="text-success fw-semibold" style="background-color: #f0fdf4; word-break: break-all;">
                                <ins class="text-decoration-none">{{ $d['new'] }}</ins>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Full Raw Properties Fallback -->
        <div class="p-3 border rounded bg-light text-center">
            @if(!empty($log->properties) && count($log->properties) > 0)
                <pre class="text-start bg-dark text-white p-3 rounded small mb-0" dir="ltr" style="max-height: 250px; overflow-y: auto;"><code>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            @else
                <p class="text-muted mb-0">{{ __('لا توجد حقول تفصيلية مسجلة لهذه العملية.') }}</p>
            @endif
        </div>
    @endif
</div>
