@extends('layouts.app')
@section('title', 'Subir Documento')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.documentos.index') }}">Gestión</a></li>
    <li class="breadcrumb-item active">Subir Documento</li>
@endsection

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-primary);color:white;">
                    <h5 class="mb-0">➕ Subir nuevo documento</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.documentos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            {{-- PASO 1: Categoría --}}
                            <div class="col-12">
                                <div class="p-3 rounded"
                                    style="background:var(--ccv-light);border-left:4px solid var(--ccv-primary);">
                                    <label class="form-label fw-bold" style="color:var(--ccv-primary);">
                                        📁 Paso 1 — Seleccionar Categoría *
                                    </label>
                                    <select name="categoria_id" id="categoria_id"
                                        class="form-select @error('categoria_id') is-invalid @enderror" required>
                                        <option value="">Seleccionar categoría...</option>
                                        @foreach ($categorias as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- PASO 2: Año --}}
                            <div class="col-12" id="bloque_anio" style="display:none;">
                                <div class="p-3 rounded"
                                    style="background:var(--ccv-light);border-left:4px solid var(--ccv-secondary);">
                                    <label class="form-label fw-bold" style="color:var(--ccv-secondary);">
                                        📅 Paso 2 — Seleccionar Año *
                                    </label>
                                    <select name="subcategoria_id" id="subcategoria_id" class="form-select" required>
                                        <option value="">Seleccionar año...</option>
                                    </select>
                                </div>
                            </div>

                            {{-- PASO 3: Mes --}}
                            <div class="col-12" id="bloque_mes" style="display:none;">
                                <div class="p-3 rounded"
                                    style="background:var(--ccv-light);border-left:4px solid var(--ccv-accent);">
                                    <label class="form-label fw-bold" style="color:#b37800;">
                                        📆 Paso 3 — Seleccionar Mes *
                                    </label>
                                    <select name="mes_id" id="mes_id" class="form-select" required>
                                        <option value="">Seleccionar mes...</option>
                                    </select>
                                </div>
                            </div>

                            {{-- PASO 4: Datos --}}
                            <div class="col-12" id="bloque_datos" style="display:none;">
                                <div class="p-3 rounded" style="background:var(--ccv-light);border-left:4px solid #28a745;">
                                    <label class="form-label fw-bold text-success">
                                        📄 Paso 4 — Datos del documento
                                    </label>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Título *</label>
                                            <input type="text" name="titulo"
                                                class="form-control @error('titulo') is-invalid @enderror"
                                                value="{{ old('titulo') }}" placeholder="Ej: Resolución 001 de 2024"
                                                required>
                                            @error('titulo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Número</label>
                                            <input type="text" name="numero_documento" class="form-control"
                                                value="{{ old('numero_documento') }}" placeholder="Ej: RES-2024-001">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Fecha</label>
                                            <input type="date" name="fecha_documento" class="form-control"
                                                value="{{ old('fecha_documento', date('Y-m-d')) }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Descripción (opcional)</label>
                                            <textarea name="descripcion" class="form-control" rows="2" placeholder="Breve descripción...">{{ old('descripcion') }}</textarea>
                                        </div>
                                        <label class="form-label fw-semibold">Archivos *</label>
                                        <input type="file" name="archivos[]"
                                            class="form-control @error('archivos') is-invalid @enderror"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx" multiple required>
                                        <div class="form-text">PDF, Word, Excel. Máximo 20MB por archivo. Puedes seleccionar
                                            varios a la vez.
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="bloque_botones" style="display:none;">
                            <hr class="my-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.documentos.index') }}" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn text-white" style="background:var(--ccv-primary);">
                                    📤 Subir documentos
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.getElementById('categoria_id').addEventListener('change', function() {
            const categoriaId = this.value;

            document.getElementById('subcategoria_id').innerHTML = '<option value="">Seleccionar año...</option>';
            document.getElementById('mes_id').innerHTML = '<option value="">Seleccionar mes...</option>';
            document.getElementById('bloque_anio').style.display = 'none';
            document.getElementById('bloque_mes').style.display = 'none';
            document.getElementById('bloque_datos').style.display = 'none';
            document.getElementById('bloque_botones').style.display = 'none';

            if (!categoriaId) return;

            fetch(`/admin/documentos-anios?categoria_id=${categoriaId}`)
                .then(r => r.json())
                .then(anios => {
                    const select = document.getElementById('subcategoria_id');
                    select.innerHTML = '<option value="">Seleccionar año...</option>';
                    anios.forEach(a => {
                        select.innerHTML += `<option value="${a.id}">${a.anio}</option>`;
                    });
                    document.getElementById('bloque_anio').style.display = 'block';
                })
                .catch(e => console.error('Error cargando años:', e));
        });

        document.getElementById('subcategoria_id').addEventListener('change', function() {
            const subcategoriaId = this.value;

            document.getElementById('mes_id').innerHTML = '<option value="">Seleccionar mes...</option>';
            document.getElementById('bloque_mes').style.display = 'none';
            document.getElementById('bloque_datos').style.display = 'none';
            document.getElementById('bloque_botones').style.display = 'none';

            if (!subcategoriaId) return;

            fetch(`/admin/documentos-meses?subcategoria_id=${subcategoriaId}`)
                .then(r => r.json())
                .then(meses => {
                    const select = document.getElementById('mes_id');
                    select.innerHTML = '<option value="">Seleccionar mes...</option>';
                    meses.forEach(m => {
                        select.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
                    });
                    document.getElementById('bloque_mes').style.display = 'block';
                })
                .catch(e => console.error('Error cargando meses:', e));
        });

        document.getElementById('mes_id').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('bloque_datos').style.display = 'block';
                document.getElementById('bloque_botones').style.display = 'block';
            } else {
                document.getElementById('bloque_datos').style.display = 'none';
                document.getElementById('bloque_botones').style.display = 'none';
            }
        });
    </script>
@endpush
