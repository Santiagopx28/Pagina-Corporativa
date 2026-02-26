@extends('layouts.app')
@section('title', 'Inicio')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inicio</li>
@endsection

@section('content')

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2rem;">📄</div>
                    <div>
                        <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">{{ $totalDocumentos }}</h4>
                        <small class="text-muted">Documentos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2rem;">📁</div>
                    <div>
                        <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">{{ $categorias->count() }}</h4>
                        <small class="text-muted">Categorías</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Módulos desplegables con sublistas por año --}}
    <h5 class="fw-bold mb-3" style="color:var(--ccv-primary);">📂 Módulos de documentos</h5>

    @foreach ($categorias as $cat)
        <div class="mb-3">

            {{-- Cabecera del módulo principal --}}
            <div class="modulo-header shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    📁 <strong>{{ $cat->nombre }}</strong>
                    <span class="badge" style="background:var(--ccv-accent);color:#333;font-size:.65rem;">
                        {{-- CAMBIO AQUÍ: Usamos el alias definido en el controlador --}}
                        {{ $cat->documentos_count }} docs
                    </span>
                </div>
                <svg class="modulo-arrow" width="18" height="18" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Cuerpo del módulo con sublistas por año --}}
            <div class="modulo-body">
                <div class="card border-0 shadow-sm" style="border-radius:0 0 8px 8px;">
                    <div class="card-body p-3">

                        {{-- CAMBIO AQUÍ: Cambiamos subcategoriasActivas por subcategorias --}}
                        @if ($cat->subcategorias->count())
                            <div class="row g-2">
                                {{-- CAMBIO AQUÍ: Recorremos subcategorias (filtradas ya en el controlador) --}}
                                @foreach ($cat->subcategorias as $sub)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <a href="{{ route('portal.subcategoria', [$cat->slug, $sub->slug]) }}"
                                            class="card border text-decoration-none h-100 sub-card">
                                            <div
                                                class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="fw-bold" style="color:var(--ccv-primary);font-size:.95rem;">
                                                        📅 {{ $sub->anio }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{-- CAMBIO AQUÍ: Usamos documentos_count --}}
                                                        {{ $sub->documentos_count }} docs
                                                    </small>
                                                </div>
                                                <svg width="14" height="14" fill="none" stroke="#94a3b8"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-end mt-3">
                                <a href="{{ route('portal.categoria', $cat->slug) }}" class="btn btn-sm"
                                    style="background:var(--ccv-primary);color:#fff;">
                                    Ver todos los documentos →
                                </a>
                            </div>
                        @else
                            <p class="text-muted small mb-0">Sin documentos en esta categoría aún.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
