@extends('layouts.app')
@section('title', 'Editar Documento')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.documentos.index') }}">Gestión</a></li>
    <li class="breadcrumb-item active">Editar Documento</li>
@endsection

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-primary);color:white;">
                    <h5 class="mb-0">✏️ Editar documento</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.documentos.update', $documento) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Título del documento *</label>
                                <input type="text" name="titulo"
                                    class="form-control @error('titulo') is-invalid @enderror"
                                    value="{{ old('titulo', $documento->titulo) }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Número del documento</label>
                                <input type="text" name="numero_documento" class="form-control"
                                    value="{{ old('numero_documento', $documento->numero_documento) }}"
                                    placeholder="Ej: RES-2024-001">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Categoría *</label>
                                <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror"
                                    required>
                                    <option value="">Seleccionar categoría...</option>
                                    @foreach ($categorias as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('categoria_id', $documento->categoria_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Fecha del documento</label>
                                <input type="date" name="fecha_documento" class="form-control"
                                    value="{{ old('fecha_documento', $documento->fecha_documento?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Estado *</label>
                                <select name="estado" class="form-select" required>
                                    <option value="activo" {{ $documento->estado == 'activo' ? 'selected' : '' }}>Activo
                                    </option>
                                    <option value="inactivo" {{ $documento->estado == 'inactivo' ? 'selected' : '' }}>
                                        Inactivo</option>
                                    <option value="archivado" {{ $documento->estado == 'archivado' ? 'selected' : '' }}>
                                        Archivado</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Descripción (opcional)</label>
                                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $documento->descripcion) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Reemplazar archivo (opcional)</label>
                                <input type="file" name="archivo"
                                    class="form-control @error('archivo') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <div class="form-text">
                                    Archivo actual: <strong>{{ $documento->archivo_nombre }}</strong>
                                    ({{ $documento->tamaño_formateado }}).
                                    Solo sube un nuevo archivo si deseas reemplazarlo.
                                </div>
                                @error('archivo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.documentos.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn text-white" style="background:var(--ccv-primary);">
                                💾 Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
