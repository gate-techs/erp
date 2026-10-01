@php
    $paginator = $records ?? $attendances ?? null;
    $isRtl = app()->getLocale() === 'ar';
    $firstIcon = $isRtl ? 'bx bx-chevrons-right' : 'bx bx-chevrons-left';
    $prevIcon  = $isRtl ? 'bx bx-chevron-right'  : 'bx bx-chevron-left';
    $nextIcon  = $isRtl ? 'bx bx-chevron-left'   : 'bx bx-chevron-right';
    $lastIcon  = $isRtl ? 'bx bx-chevrons-left'  : 'bx bx-chevrons-right';
@endphp

@if($paginator && method_exists($paginator, 'total') && $paginator->total() > 0)
    <div class="card-footer bg-transparent border-top py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            {{-- Left Side: Records Info & Rows Per Page Dropdown --}}
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="text-muted small">
                    {{ __('عرض السجلات من') }}
                    <span class="fw-bold text-dark">{{ $paginator->firstItem() ?? 0 }}</span>
                    {{ __('إلى') }}
                    <span class="fw-bold text-dark">{{ $paginator->lastItem() ?? 0 }}</span>
                    {{ __('من إجمالي') }}
                    <span class="badge bg-label-primary px-2 py-1 fw-bold fs-tiny">{{ number_format($paginator->total()) }}</span>
                    {{ __('سجل') }}
                </div>

                <div class="d-flex align-items-center gap-2 border-start pe-2 ps-3">
                    <label for="reportPerPageSelect" class="small text-muted mb-0 text-nowrap">{{ __('لكل صفحة:') }}</label>
                    <select id="reportPerPageSelect" class="form-select form-select-sm w-auto" onchange="changeReportPerPage(this.value)">
                        @foreach([15, 25, 50, 100, 250] as $size)
                            <option value="{{ $size }}" {{ (request('per_page', 25) == $size) ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Right Side: Pagination Links & Jump to Page --}}
            @if($paginator->hasPages())
                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if($paginator->lastPage() > 3)
                        <div class="d-none d-md-flex align-items-center gap-1">
                            <span class="small text-muted text-nowrap">{{ __('انتقال:') }}</span>
                            <input type="number" min="1" max="{{ $paginator->lastPage() }}" 
                                   class="form-control form-control-sm text-center" 
                                   style="width: 65px;" 
                                   placeholder="{{ $paginator->currentPage() }}" 
                                   id="reportJumpPageInput"
                                   onkeydown="if(event.key === 'Enter'){ event.preventDefault(); jumpToReportPage(this.value, {{ $paginator->lastPage() }}); }">
                            <button type="button" class="btn btn-sm btn-icon btn-label-secondary" 
                                    onclick="jumpToReportPage(document.getElementById('reportJumpPageInput').value, {{ $paginator->lastPage() }})"
                                    title="{{ __('انتقال إلى الصفحة') }}">
                                <i class="{{ $nextIcon }}"></i>
                            </button>
                        </div>
                    @endif

                    <nav aria-label="{{ __('التنقل بين الصفحات') }}">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- First & Previous Links --}}
                            @if ($paginator->onFirstPage())
                                <li class="page-item disabled"><span class="page-link"><i class="{{ $firstIcon }}"></i></span></li>
                                <li class="page-item disabled"><span class="page-link"><i class="{{ $prevIcon }}"></i></span></li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $paginator->url(1) }}" title="{{ __('الصفحة الأولى') }}">
                                        <i class="{{ $firstIcon }}"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="{{ __('السابق') }}">
                                        <i class="{{ $prevIcon }}"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Numbered Page Links (Sliding Window) --}}
                            @php
                                $cur = $paginator->currentPage();
                                $last = $paginator->lastPage();
                                $start = max(1, $cur - 2);
                                $end = min($last, $cur + 2);
                                if ($end - $start < 4) {
                                    if ($start === 1) {
                                        $end = min($last, $start + 4);
                                    } elseif ($end === $last) {
                                        $start = max(1, $end - 4);
                                    }
                                }
                            @endphp

                            @if($start > 1)
                                <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                                @if($start > 2)
                                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                @endif
                            @endif

                            @for ($p = $start; $p <= $end; $p++)
                                @if ($p == $cur)
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $p }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($p) }}">{{ $p }}</a></li>
                                @endif
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)
                                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                @endif
                                <li class="page-item"><a class="page-link" href="{{ $paginator->url($last) }}">{{ $last }}</a></li>
                            @endif

                            {{-- Next & Last Links --}}
                            @if ($paginator->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="{{ __('التالي') }}">
                                        <i class="{{ $nextIcon }}"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ $paginator->url($last) }}" title="{{ __('الصفحة الأخيرة') }}">
                                        <i class="{{ $lastIcon }}"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled"><span class="page-link"><i class="{{ $nextIcon }}"></i></span></li>
                                <li class="page-item disabled"><span class="page-link"><i class="{{ $lastIcon }}"></i></span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <script>
        if (typeof window.changeReportPerPage === 'undefined') {
            window.changeReportPerPage = function(val) {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', val);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            };
        }
        if (typeof window.jumpToReportPage === 'undefined') {
            window.jumpToReportPage = function(val, maxPage) {
                const p = parseInt(val);
                if (!p || p < 1) return;
                if (maxPage && p > maxPage) return;
                const url = new URL(window.location.href);
                url.searchParams.set('page', p);
                window.location.href = url.toString();
            };
        }
    </script>
@endif
