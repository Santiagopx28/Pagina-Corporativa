@extends('layouts.app')
@section('title', 'Editar Mes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.meses.index') }}">Gestión de Meses</a></li>
    <li class="breadcrumb-item active">Editar Mes</li>
@endsection

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-primary);color:white;">
                    <h5 class="mb-0">✏️ Editar mes</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.meses.update', $mes) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Año (Subcategoría) *</label>
                            <select name="subcategoria_id"
                                class="form-select @error('subcategoria_id') is-invalid @enderror" required>
                                <option value="">Seleccionar año...</option>
                                @foreach ($subcategorias as $catNombre => $subs)
                                    <optgroup label="{{ $catNombre }}">
                                        @foreach ($subs as $sub)
                                            <option value="{{ $sub->id }}"
                                                {{ old('subcategoria_id', $mes->subcategoria_id) == $sub->id ? 'selected' : '' }}>
                                                {{ $sub->anio }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('subcategoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mes *</label>
                            <select name="numero_mes" class="form-select @error('numero_mes') is-invalid @enderror"
                                required>
                                <option value="">Seleccionar mes...</option>
                                @foreach ($mesesNombres as $num => $nombre)
                                    <option value="{{ $num }}"
                                        {{ old('numero_mes', $mes->numero_mes) == $num ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('numero_mes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="activo" id="activo" class="form-check-input"
                                    {{ old('activo', $mes->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Mes activo (visible en el portal)
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.meses.index') }}" class="btn btn-outline-secondary">
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
