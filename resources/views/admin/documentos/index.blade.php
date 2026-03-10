@extends('layouts.app')
@section('title', 'Gestión de Documentos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Gestión de Documentos</li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">⚙️ Gestión de Documentos</h4>
        <a href="{{ route('admin.documentos.create') }}" class="btn"
            style="background:var(--ccv-accent);color:#333;font-weight:600;">
            ➕ Subir nuevo documentos
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:var(--ccv-light);">
                        <tr>
                            <th class="px-4 py-3">Título</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3">Año</th>
                            <th class="py-3">Mes</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3">Descargas</th>
                            <th class="py-3">Fecha subida</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">{{ Str::limit($doc->titulo, 60) }}</div>
                                    <small class="text-muted">{{ $doc->numero_documento }}</small>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-info text-dark">{{ $doc->categoria->nombre }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="badge {{ $doc->subcategoria ? 'bg-secondary' : 'bg-warning text-dark' }}">
                                        {{ $doc->subcategoria->anio ?? 'Sin año' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if ($doc->mes)
                                        <span class="badge bg-primary">{{ $doc->mes->nombre }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Sin mes</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if ($doc->estado == 'activo')
                                        <span class="badge bg-success">Activo</span>
                                    @elseif($doc->estado == 'inactivo')
                                        <span class="badge bg-warning text-dark">Inactivo</span>
                                    @else
                                        <span class="badge bg-secondary">Archivado</span>
                                    @endif
                                </td>
                                <td class="py-3 text-muted small">{{ $doc->descargas }}</td>
                                <td class="py-3 text-muted small">{{ $doc->created_at->format('d/m/Y') }}</td>
                                <td class="py-3">
                                    <div class="d-flex gap-1">
                                        {{-- Botón Ver/Descargar --}}
                                        <a href="{{ route('portal.descargar', $doc->id) }}"
                                            class="btn btn-sm btn-outline-success" title="Ver/Descargar documento"
                                            target="_blank">
                                            👁️
                                        </a>

                                        {{-- Botón Editar --}}
                                        <a href="{{ route('admin.documentos.edit', $doc) }}"
                                            class="btn btn-sm btn-outline-primary" title="Editar documento">
                                            ✏️
                                        </a>

                                        {{-- Botón Eliminar --}}
                                        <form action="{{ route('admin.documentos.destroy', $doc) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('⚠️ ¿Eliminar \"{{ $doc->titulo }}\"?\n\nEsta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Eliminar documento">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No hay documentos aún.
                                    <a href="{{ route('admin.documentos.create') }}">Subir el primero</a>
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
