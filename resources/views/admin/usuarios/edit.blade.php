@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Editar Usuario</li>
@endsection

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-primary);color:white;">
                    <h5 class="mb-0">✏️ Editar usuario</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre completo *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $usuario->name) }}" placeholder="Ej: Juan Pérez" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $usuario->email) }}" placeholder="Ej: juan@ccv.org.co" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        <p class="text-muted small mb-3">
                            💡 <strong>Cambiar contraseña:</strong> Déjalo en blanco si no deseas cambiarla.
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nueva contraseña (opcional)</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Repite la contraseña">
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
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
