@if ($paginator->hasPages())
    @php
        $isRtl = app()->getLocale() === 'ar';
        $prevIcon = $isRtl ? 'bx bx-chevron-right' : 'bx bx-chevron-left';
        $nextIcon = $isRtl ? 'bx bx-chevron-left' : 'bx bx-chevron-right';
    @endphp
    <nav class="d-flex flex-wrap align-items-center justify-content-between gap-2" aria-label="{{ __('التنقل بين الصفحات') }}">
        <div class="d-none d-sm-block text-muted small">
            @if ($isRtl)
                {{ __('عرض') }} <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> {{ __('إلى') }} <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> {{ __('من إجمالي') }} <span class="fw-semibold text-dark">{{ number_format($paginator->total()) }}</span> {{ __('سجل') }}
            @else
                Showing <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> to <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> of <span class="fw-semibold text-dark">{{ number_format($paginator->total()) }}</span> results
            @endif
        </div>

        <div>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link"><i class="{{ $prevIcon }}"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="{{ __('السابق') }}">
                            <i class="{{ $prevIcon }}"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="{{ __('التالي') }}">
                            <i class="{{ $nextIcon }}"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link"><i class="{{ $nextIcon }}"></i></span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
