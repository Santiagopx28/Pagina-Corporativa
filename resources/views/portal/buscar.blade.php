@extends('layouts.app')
@section('title', 'Buscar documentos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Buscar</li>
@endsection

@section('content')

    {{-- Formulario de búsqueda --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('portal.buscar') }}">
                <div class="row g-2">
                    <div class="col-md-7">
                        <input type="text" name="q" class="form-control"
                            placeholder="Buscar por título, número o descripción..." value="{{ $q }}">
                    </div>
                    <div class="col-md-3">
                        <select name="categoria" class="form-select">
                            <option value="">Todas las categorías</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn w-100 text-white" style="background:var(--ccv-primary);">
                            🔍 Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Resultados --}}
    @if ($q)
        <p class="text-muted mb-3">
            {{ $documentos->total() }} resultado(s) para: <strong>{{ $q }}</strong>
        </p>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:var(--ccv-light);">
                        <tr>
                            <th class="px-4 py-3">Documento</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3">Número</th>
                            <th class="py-3">Fecha</th>
                            <th class="py-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">{{ $doc->titulo }}</div>
                                    @if ($doc->descripcion)
                                        <div class="text-muted small">{{ Str::limit($doc->descripcion, 80) }}</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-info text-dark">{{ $doc->categoria->nombre }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-secondary">{{ $doc->numero_documento ?? 'S/N' }}</span>
                                </td>
                                <td class="py-3 text-muted small">
                                    {{ $doc->fecha_documento?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('portal.descargar', $doc->id) }}" class="btn btn-sm"
                                        style="background:var(--ccv-accent);color:#333;">
                                        ⬇️ Descargar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No se encontraron documentos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $documentos->links() }}
    </div>

@endsection
