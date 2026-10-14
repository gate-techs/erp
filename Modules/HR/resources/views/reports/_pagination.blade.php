@php
    $paginator = $records ?? $attendances ?? null;
@endphp

@if($paginator && method_exists($paginator, 'total') && $paginator->total() > 0)
    <div class="card-footer bg-transparent border-top py-3">
        {{ $paginator->links('pagination::app') }}
    </div>
@endif
