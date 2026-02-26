@extends('layouts.app')
@section('title', 'Agregar Mes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.meses.index') }}">Gestión de Meses</a></li>
    <li class="breadcrumb-item active">Agregar Mes</li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">➕ Agregar nuevo mes</h4>
    </div>

    @foreach ($subcategorias as $catNombre => $subs)
        <div class="mb-3">
            {{-- Cabecera categoría desplegable --}}
            <div class="modulo-header shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    📁 <strong>{{ $catNombre }}</strong>
                    <span class="badge" style="background:var(--ccv-accent);color:#333;">
                        {{ $subs->count() }} años
                    </span>
                </div>
                <svg class="modulo-arrow" width="18" height="18" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Años dentro de la categoría --}}
            <div class="modulo-body">
                <div class="card border-0 shadow-sm p-3" style="border-radius:0 0 8px 8px;">

                    @foreach ($subs as $sub)
                        <div class="mb-3">
                            {{-- Cabecera año desplegable --}}
                            <div class="modulo-header-sub d-flex align-items-center justify-content-between p-2 rounded mb-2"
                                style="background:var(--ccv-secondary);color:white;cursor:pointer;"
                                onclick="toggleSub(this)">
                                <div class="d-flex align-items-center gap-2">
                                    📅 <strong>{{ $sub->anio }}</strong>
                                </div>
                                <svg class="sub-arrow" width="16" height="16" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" style="transition:transform .3s;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            {{-- Formulario de meses --}}
                            <div class="sub-body" style="display:none;">
                                <form action="{{ route('admin.meses.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="subcategoria_id" value="{{ $sub->id }}">

                                    <div class="row g-2 align-items-end px-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Seleccionar mes *</label>
                                            <select name="numero_mes" class="form-select" required>
                                                <option value="">Seleccionar mes...</option>
                                                @foreach ($mesesNombres as $num => $nombre)
                                                    <option value="{{ $num }}">{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn w-100 text-white"
                                                style="background:var(--ccv-primary);">
                                                💾 Guardar mes para {{ $sub->anio }}
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Meses ya existentes --}}
                                    @if ($sub->meses->count())
                                        <div class="mt-3 px-2">
                                            <small class="text-muted fw-semibold">Meses existentes:</small>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach ($sub->meses->sortBy('numero_mes') as $mesExistente)
                                                    <span class="badge"
                                                        style="background:var(--ccv-accent);color:#333;font-size:.75rem;">
                                                        📆 {{ $mesExistente->nombre }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                </form>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        function toggleSub(header) {
            const body = header.nextElementSibling;
            const arrow = header.querySelector('.sub-arrow');
            const isOpen = body.style.display === 'block';
            body.style.display = isOpen ? 'none' : 'block';
            arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    </script>
@endpush
