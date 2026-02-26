@extends('layouts.app')
@section('title', $categoria->nombre)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">{{ $categoria->nombre }}</li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">📁 {{ $categoria->nombre }}</h4>
    </div>

    {{-- Sublistas de años --}}
    @if ($subcategorias->count())
        <div class="row g-3 mb-4">
            @foreach ($subcategorias as $sub)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('portal.subcategoria', [$categoria->slug, $sub->slug]) }}"
                        class="card border text-decoration-none h-100 sub-card">
                        <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-bold" style="color:var(--ccv-primary);font-size:.95rem;">
                                    📅 {{ $sub->anio }}
                                </div>
                                <small class="text-muted">
                                    <span class="fw-bold"
                                        style="color:var(--ccv-primary);">{{ $sub->documentos_count ?? 0 }}</span>
                                    documentos
                                </small>
                            </div>
                            <svg width="14" height="14" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <div style="font-size:2rem;">📭</div>
                No hay años configurados en esta categoría aún.
            </div>
        </div>
    @endif

    {{-- Tabla de TODOS los documentos (opcional) --}}
    @if ($documentos->count())
        <hr class="my-4">
        <h5 class="fw-bold mb-3" style="color:var(--ccv-primary);">📄 Todos los documentos</h5>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:var(--ccv-light);">
                            <tr>
                                <th class="px-4 py-3">Documento</th>
                                <th class="py-3">Año</th>
                                <th class="py-3">Número</th>
                                <th class="py-3">Fecha</th>
                                <th class="py-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documentos as $doc)
                                <tr>
                                    <td class="px-4 py-3" data-label="Documento">
                                        <div class="fw-semibold">{{ $doc->titulo }}</div>
                                        @if ($doc->descripcion)
                                            <div class="text-muted small">{{ Str::limit($doc->descripcion, 80) }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3" data-label="Año">
                                        @if ($doc->subcategoria)
                                            <span class="badge bg-info">{{ $doc->subcategoria->anio }}</span>
                                        @else
                                            <span class="badge bg-secondary">S/A</span>
                                        @endif
                                    </td>
                                    <td class="py-3" data-label="Número">
                                        <span class="badge bg-secondary">{{ $doc->numero_documento ?? 'S/N' }}</span>
                                    </td>
                                    <td class="py-3 text-muted small" data-label="Fecha">
                                        {{ $doc->fecha_documento?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="py-3" data-label="Acción">
                                        <a href="{{ route('portal.descargar', $doc->id) }}" class="btn btn-sm"
                                            style="background:var(--ccv-accent);color:#333;">
                                            ⬇️ Descargar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $documentos->links() }}
        </div>
    @endif

@endsection

@push('scripts')
    <style>
        .sub-card {
            transition: transform .2s, box-shadow .2s;
            border-left: 3px solid var(--ccv-accent) !important;
        }

        .sub-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        }
    </style>
@endpush
