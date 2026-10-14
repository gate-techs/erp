{{--
    Unified app pagination: mirrors the global DataTables footer (info + "simple_numbers"
    paging) and reuses its .dt-info / .dt-paging styles from app.css, so server-rendered
    lists look exactly like DataTables lists (e.g. hr/settings/administrations).
--}}
@php
    $isAr = app()->getLocale() === 'ar';
    $hasTotal = method_exists($paginator, 'total');
    $cur = $paginator->currentPage();
    $last = $hasTotal ? max(1, $paginator->lastPage()) : null;

    // Same window as DataTables "simple_numbers" (numbers_length = 7, boundary numbers on)
    $numbers = [];
    if ($hasTotal) {
        if ($last <= 7) {
            $numbers = range(1, $last);
        } elseif ($cur <= 4) {
            $numbers = array_merge(range(1, 5), ['…', $last]);
        } elseif ($cur >= $last - 3) {
            $numbers = array_merge([1, '…'], range($last - 4, $last));
        } else {
            $numbers = [1, '…', $cur - 1, $cur, $cur + 1, '…', $last];
        }
    }
@endphp

<div class="app-pagination">
    @if ($hasTotal)
        <div class="dt-info" role="status" aria-live="polite">
            @if ($isAr)
                عرض {{ $paginator->firstItem() ?? 0 }} إلى {{ $paginator->lastItem() ?? 0 }} من {{ number_format($paginator->total()) }} سجل
            @else
                Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ number_format($paginator->total()) }} entries
            @endif
        </div>
    @endif

    <div class="dt-paging paging_simple_numbers">
        <nav aria-label="pagination">
            <ul class="pagination">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link previous" aria-disabled="true" aria-label="Previous"><i class="icon-base bx bx-chevron-left"></i></span></li>
                @else
                    <li class="page-item"><a class="page-link previous" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous"><i class="icon-base bx bx-chevron-left"></i></a></li>
                @endif

                {{-- Numbers (boundary numbers / ellipses are hidden on small screens) --}}
                @foreach ($numbers as $n)
                    @php $isBoundary = !is_int($n) || (($n === 1 || $n === $last) && abs($n - $cur) > 1 && count($numbers) > 5); @endphp
                    @if (!is_int($n))
                        <li class="page-item disabled pg-far"><span class="page-link ellipsis">&#x2026;</span></li>
                    @elseif ($n === $cur)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $n }}</span></li>
                    @else
                        <li class="page-item {{ $isBoundary ? 'pg-far' : '' }}"><a class="page-link" href="{{ $paginator->url($n) }}">{{ $n }}</a></li>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link next" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next"><i class="icon-base bx bx-chevron-right"></i></a></li>
                @else
                    <li class="page-item disabled"><span class="page-link next" aria-disabled="true" aria-label="Next"><i class="icon-base bx bx-chevron-right"></i></span></li>
                @endif
            </ul>
        </nav>
    </div>
</div>
