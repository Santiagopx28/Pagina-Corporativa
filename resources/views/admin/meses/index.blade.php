@extends('layouts.app')
@section('title', 'Gestión de Meses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Gestión de Meses</li>
@endsection

@section('content')

    {{-- 1. ENCABEZADO Y BOTÓN PRINCIPAL --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">📆 Gestión de Meses</h4>
                <p class="text-muted small mb-0">Organiza los meses por cada año y categoría documental</p>
            </div>
            <button type="button" class="btn shadow-sm" style="background:var(--ccv-accent);color:#333;font-weight:600;"
                data-bs-toggle="modal" data-bs-target="#modalCrearMes">
                ➕ Agregar Nuevo Mes
            </button>
        </div>
    </div>

    {{-- 2. LISTADO DE CATEGORÍAS --}}
    @if (isset($categorias) && $categorias->count() > 0)
        @foreach ($categorias as $cat)
            <div class="mb-3">

                {{-- Cabecera categoría (acordeón principal — usa modulo-header del layout) --}}
                <div class="modulo-header shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:1.2rem;">📁</span>
                        <strong>{{ $cat->nombre }}</strong>
                        <span class="badge bg-light text-dark border ms-2">
                            {{ $cat->subcategorias->count() }} años
                        </span>
                    </div>
                    <svg class="modulo-arrow" width="18" height="18" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                {{-- Años dentro de la categoría --}}
                <div class="modulo-body mt-1">
                    <div class="card border-0 shadow-sm p-3" style="border-radius:0 0 8px 8px;">

                        @forelse ($cat->subcategorias as $sub)
                            <div class="mb-3">

                                {{-- Cabecera año (acordeón secundario) --}}
                                <div class="modulo-header-sub d-flex align-items-center justify-content-between p-2 rounded mb-1"
                                    style="background:var(--ccv-secondary);color:white;cursor:pointer;"
                                    onclick="toggleSub(this)">
                                    <div class="d-flex align-items-center gap-2">
                                        📅 <strong>{{ $sub->anio }}</strong>
                                        <span class="badge bg-light text-dark">{{ $sub->meses->count() }} meses</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                                        {{-- Botón rápido añadir mes a este año --}}
                                        <button class="btn btn-sm btn-outline-light py-0 px-2"
                                            onclick="setSubcategoria({{ $sub->id }})" data-bs-toggle="modal"
                                            data-bs-target="#modalCrearMes">
                                            + Añadir
                                        </button>
                                        <svg class="sub-arrow" width="16" height="16" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24" style="transition:transform .3s;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Meses del año --}}
                                <div class="sub-body" style="display:none;">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead style="background:var(--ccv-light);">
                                                <tr>
                                                    <th class="px-3 py-2">Mes</th>
                                                    <th class="py-2">Estado</th>
                                                    <th class="py-2">Docs</th>
                                                    <th class="py-2 text-end px-3">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($sub->meses as $mes)
                                                    <tr>
                                                        <td class="px-3 py-2">
                                                            <span style="color:var(--ccv-primary);">📆
                                                                {{ $mes->nombre }}</span>
                                                        </td>
                                                        <td class="py-2">
                                                            <span
                                                                class="badge {{ $mes->activo ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $mes->activo ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td class="py-2 text-muted small">
                                                            {{ $mes->documentos_count ?? 0 }} docs
                                                        </td>
                                                        <td class="py-2 text-end px-3">
                                                            <div class="d-flex gap-2 justify-content-end">
                                                                <a href="{{ route('admin.meses.edit', $mes) }}"
                                                                    class="btn btn-sm btn-light">✏️</a>
                                                                <form action="{{ route('admin.meses.destroy', $mes) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('¿Eliminar {{ $mes->nombre }}?')">
                                                                    @csrf @method('DELETE')
                                                                    <button
                                                                        class="btn btn-sm btn-light text-danger">🗑️</button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-3 text-muted small">
                                                            Sin meses configurados.
                                                            <button class="btn btn-sm btn-link p-0 ms-1"
                                                                onclick="setSubcategoria({{ $sub->id }})"
                                                                data-bs-toggle="modal" data-bs-target="#modalCrearMes">
                                                                Agregar uno
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="text-center py-3 text-muted small">
                                No hay años configurados en esta categoría.
                            </div>
                        @endforelse

                    </div>
                </div>

            </div>
        @endforeach
    @else
        <div class="alert alert-info">No se encontraron categorías activas para gestionar.</div>
    @endif


    {{-- 3. MODAL AGREGAR MES --}}
    <div class="modal fade" id="modalCrearMes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.meses.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header" style="background:var(--ccv-primary);">
                    <h5 class="modal-title text-white">📆 Agregar Mes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Selector Categoría --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                        <select id="modal_categoria" class="form-select" required onchange="filtrarAnios()">
                            <option value="">Seleccione una categoría...</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector Año (filtrado por categoría) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Año <span class="text-danger">*</span></label>
                        <select name="subcategoria_id" id="select_subcategoria_id" class="form-select" required>
                            <option value="">Primero seleccione una categoría...</option>
                        </select>
                    </div>

                    {{-- Selector Mes --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mes <span class="text-danger">*</span></label>
                        <select name="numero_mes" class="form-select" required>
                            <option value="">Seleccione un mes...</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background:var(--ccv-primary);">
                        Guardar mes
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const aniosPorCategoria = {
            @foreach ($categorias as $cat)
                {{ $cat->id }}: [
                    @foreach ($cat->subcategorias as $sub)
                        {
                            id: {{ $sub->id }},
                            anio: '{{ $sub->anio }}'
                        },
                    @endforeach
                ],
            @endforeach
        };

        function filtrarAnios(catId, preseleccionarSubcat) {
            const select = document.getElementById('select_subcategoria_id');
            const anios = aniosPorCategoria[catId] || [];

            select.innerHTML = anios.length ?
                '<option value="">Seleccione un año...</option>' :
                '<option value="">No hay años para esta categoría</option>';

            anios.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.anio;
                select.appendChild(opt);
            });

            if (preseleccionarSubcat) {
                select.value = preseleccionarSubcat;
            }
        }

        function setSubcategoria(subcatId) {
            for (const [catId, anios] of Object.entries(aniosPorCategoria)) {
                const found = anios.find(a => a.id === subcatId);
                if (found) {
                    const selectCat = document.getElementById('modal_categoria');
                    selectCat.value = catId;
                    filtrarAnios(catId, subcatId);
                    break;
                }
            }
        }

        // Acordeón secundario
        function toggleSub(header) {
            const body = header.nextElementSibling;
            const arrow = header.querySelector('.sub-arrow');
            const isOpen = body.style.display === 'block';
            body.style.display = isOpen ? 'none' : 'block';
            arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Listener del select de categoría (en vez de onchange inline)
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('modal_categoria').addEventListener('change', function() {
                filtrarAnios(this.value, null);
            });

            // Resetear modal al cerrarse
            document.getElementById('modalCrearMes').addEventListener('hidden.bs.modal', function() {
                document.getElementById('modal_categoria').value = '';
                document.getElementById('select_subcategoria_id').innerHTML =
                    '<option value="">Primero seleccione una categoría...</option>';
            });
        });
    </script>
@endpush
