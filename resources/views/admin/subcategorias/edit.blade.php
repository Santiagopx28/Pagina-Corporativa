@extends('layouts.app')
@section('title', 'Editar Año')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.subcategorias.index') }}">Gestión de Años</a></li>
    <li class="breadcrumb-item active">Editar Año</li>
@endsection

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-primary);color:white;">
                    <h5 class="mb-0">✏️ Editar año</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.subcategorias.update', $subcategoria) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror"
                                required>
                                <option value="">Seleccionar categoría...</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('categoria_id', $subcategoria->categoria_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Año *</label>
                            <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror"
                                value="{{ old('anio', $subcategoria->anio) }}" min="1967" max="2099" required>
                            @error('anio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ingresa un año entre 1967 y 2099.</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="activo" id="activo" class="form-check-input"
                                    {{ old('activo', $subcategoria->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Año activo (visible en el portal)
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.subcategorias.index') }}" class="btn btn-outline-secondary">
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
