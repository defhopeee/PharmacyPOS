@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pg-link disabled" aria-disabled="true">Prev</span>
        @else
            <a class="pg-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Prev</a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pg-link disabled">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pg-link active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pg-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a class="pg-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="pg-link disabled" aria-disabled="true">Next</span>
        @endif
    </nav>
@endif
