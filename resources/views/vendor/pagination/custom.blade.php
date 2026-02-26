@if ($paginator->hasPages())
    <nav aria-label="Paginación" class="ccv-pagination-nav">
        <div class="ccv-pagination">

            {{-- Botón anterior --}}
            @if ($paginator->onFirstPage())
                <span class="ccv-page-btn disabled">← Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="ccv-page-btn">← Anterior</a>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="ccv-page-btn dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="ccv-page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="ccv-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="ccv-page-btn">Siguiente →</a>
            @else
                <span class="ccv-page-btn disabled">Siguiente →</span>
            @endif

        </div>

        {{-- Info de resultados --}}
        <div class="ccv-pagination-info">
            Mostrando <strong>{{ $paginator->firstItem() }}</strong> –
            <strong>{{ $paginator->lastItem() }}</strong>
            de <strong>{{ $paginator->total() }}</strong> resultados
        </div>
    </nav>

    <style>
        .ccv-pagination-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            margin-top: 1.5rem;
        }

        .ccv-pagination {
            display: flex;
            align-items: center;
            gap: .3rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .ccv-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 .75rem;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 500;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: var(--ccv-primary);
            transition: all .2s;
            cursor: pointer;
        }

        .ccv-page-btn:hover:not(.disabled):not(.active):not(.dots) {
            background: var(--ccv-primary);
            color: #fff;
            border-color: var(--ccv-primary);
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(30, 58, 95, .15);
        }

        .ccv-page-btn.active {
            background: var(--ccv-primary);
            color: #fff;
            border-color: var(--ccv-primary);
            box-shadow: 0 3px 8px rgba(30, 58, 95, .2);
            cursor: default;
        }

        .ccv-page-btn.disabled {
            color: #b0bec5;
            border-color: #eee;
            background: #fafafa;
            cursor: default;
        }

        .ccv-page-btn.dots {
            border: none;
            background: transparent;
            color: #94a3b8;
            cursor: default;
            min-width: 24px;
            padding: 0;
        }

        .ccv-pagination-info {
            font-size: .78rem;
            color: #94a3b8;
        }

        .ccv-pagination-info strong {
            color: var(--ccv-primary);
        }
    </style>
@endif
