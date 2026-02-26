@extends('layouts.app')
@section('title', 'Gestión de Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Gestión de Usuarios</li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">👥 Gestión de Usuarios</h4>
        <a href="{{ route('admin.usuarios.create') }}" class="btn"
            style="background:var(--ccv-accent);color:#333;font-weight:600;">
            ➕ Crear usuario
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:var(--ccv-light);">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Documentos subidos</th>
                            <th class="py-3">Registrado</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:35px;height:35px;background:var(--ccv-primary);color:white;font-weight:bold;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            @if ($user->id === auth()->id())
                                                <span class="badge bg-success" style="font-size:.65rem;">Tú</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">{{ $user->email }}</td>
                                <td class="py-3">
                                    <span class="badge bg-info">
                                        {{ $user->documentos()->count() }} docs
                                    </span>
                                </td>
                                <td class="py-3 text-muted small">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.usuarios.edit', $user) }}"
                                            class="btn btn-sm btn-outline-primary">✏️ Editar</a>

                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar a {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $usuarios->links() }}
    </div>

@endsection
