@extends('layouts.app')
@section('title', 'Gestión de Años')

@section('content')

    {{-- 1. ENCABEZADO Y BOTÓN PRINCIPAL --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">📅 Gestión de Años</h4>
                <p class="text-muted small mb-0">Organiza los años por cada categoría documental</p>
            </div>
            {{-- Este botón debe aparecer SIEMPRE --}}
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearAnio"
                style="background:var(--ccv-primary); border:none; font-weight:600;">
                ➕ Agregar Nuevo Año
            </button>
        </div>
    </div>

    {{-- 2. LISTADO DE CATEGORÍAS --}}
    @if (isset($categorias) && $categorias->count() > 0)
        @foreach ($categorias as $cat)
            <div class="mb-3">
                <div class="anio-header shadow-sm d-flex justify-content-between align-items-center p-3 bg-white"
                    style="border-radius:8px; cursor:pointer;">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:1.2rem;">📁</span>
                        <strong>{{ $cat->nombre }}</strong>
                        <span class="badge bg-light text-dark border ms-2">
                            {{ $cat->subcategorias->count() }} años
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        {{-- Botón rápido interno --}}
                        <button class="btn btn-sm btn-outline-primary py-1"
                            onclick="event.stopPropagation(); setCategoria({{ $cat->id }})" data-bs-toggle="modal"
                            data-bs-target="#modalCrearAnio">
                            + Añadir
                        </button>
                        <i class="fas fa-chevron-down modulo-arrow"></i>
                    </div>
                </div>

                <div class="modulo-body mt-1">
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Año</th>
                                        <th>Estado</th>
                                        <th class="text-end px-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cat->subcategorias as $sub)
                                        <tr>
                                            <td class="px-4 fw-bold">{{ $sub->anio }}</td>
                                            <td>
                                                <span class="badge {{ $sub->activo ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $sub->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="text-end px-4">
                                                <a href="{{ route('admin.subcategorias.edit', $sub) }}"
                                                    class="btn btn-sm btn-light">✏️</a>
                                                <form action="{{ route('admin.subcategorias.destroy', $sub) }}"
                                                    method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger">🗑️</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3 text-muted">No hay años en esta
                                                categoría.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">No se encontraron categorías activas para gestionar.</div>
    @endif

    {{-- 3. EL MODAL (Asegúrate que esté aquí) --}}
    <div class="modal fade" id="modalCrearAnio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.subcategorias.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Año</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria_id" id="select_categoria_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach ($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Año</label>
                        <input type="number" name="anio" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Guardar</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function setCategoria(id) {
            const select = document.getElementById('select_categoria_id');
            if (select) select.value = id;
        }
    </script>
@endpush

@push('scripts')
    <script>
        // Función para el modal
        function setCategoria(id) {
            const select = document.getElementById('select_categoria_id');
            if (select) select.value = id;
        }

        // Lógica de los acordeones (Módulos)
        document.querySelectorAll('.anio-header').forEach(h => {
            h.addEventListener('click', () => {
                const body = h.closest('.mb-3').querySelector('.modulo-body');
                if (!body) return;

                const isOpen = body.classList.contains('open');

                // Cerrar otros
                document.querySelectorAll('.modulo-body').forEach(b => b.classList.remove('open'));
                document.querySelectorAll('.anio-header').forEach(x => x.classList.remove('open'));

                // Abrir el actual
                if (!isOpen) {
                    body.classList.add('open');
                    h.classList.add('open');
                }
            });
        });
    </script>
@endpush
