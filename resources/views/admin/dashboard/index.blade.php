@extends('layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

    {{-- Estadísticas generales --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">📄</div>
                    <div>
                        <h3 class="fw-bold mb-0" style="color:var(--ccv-primary);">{{ $totalDocumentos }}</h3>
                        <small class="text-muted">Total Documentos</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">📁</div>
                    <div>
                        <h3 class="fw-bold mb-0" style="color:var(--ccv-secondary);">{{ $totalCategorias }}</h3>
                        <small class="text-muted">Categorías</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">⬇️</div>
                    <div>
                        <h3 class="fw-bold mb-0" style="color:var(--ccv-accent);">{{ number_format($totalDescargas) }}</h3>
                        <small class="text-muted">Total Descargas</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">🆕</div>
                    <div>
                        <h3 class="fw-bold mb-0 text-success">{{ $documentosHoy }}</h3>
                        <small class="text-muted">Subidos Hoy</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Gráfica: Documentos por categoría --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-light);">
                    <h6 class="mb-0 fw-bold" style="color:var(--ccv-primary);">📊 Documentos por Categoría</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartCategorias" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfica: Documentos por año --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-light);">
                    <h6 class="mb-0 fw-bold" style="color:var(--ccv-primary);">📅 Documentos por Año</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartAnios" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Top 10 más descargados --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-light);">
                    <h6 class="mb-0 fw-bold" style="color:var(--ccv-primary);">🔥 Top 10 Más Descargados</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead style="background:var(--ccv-light);">
                                <tr>
                                    <th class="px-3 py-2">#</th>
                                    <th class="py-2">Documento</th>
                                    <th class="py-2">Categoría</th>
                                    <th class="py-2 text-end">Descargas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDescargados as $index => $doc)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <span class="badge" style="background:var(--ccv-accent);color:#333;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <div class="fw-semibold small">{{ Str::limit($doc->titulo, 40) }}</div>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-info text-dark">{{ $doc->categoria->nombre }}</span>
                                        </td>
                                        <td class="py-2 text-end fw-bold" style="color:var(--ccv-accent);">
                                            {{ $doc->descargas }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            No hay descargas registradas aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actividad reciente --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3" style="background:var(--ccv-light);">
                    <h6 class="mb-0 fw-bold" style="color:var(--ccv-primary);">⏱️ Actividad Reciente</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead style="background:var(--ccv-light);">
                                <tr>
                                    <th class="px-3 py-2">Documento</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2 text-end">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recientes as $doc)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="fw-semibold small">{{ Str::limit($doc->titulo, 35) }}</div>
                                            <span class="badge bg-info text-dark" style="font-size:.65rem;">
                                                {{ $doc->categoria->nombre }}
                                            </span>
                                        </td>
                                        <td class="py-2 small text-muted">{{ $doc->autor->name }}</td>
                                        <td class="py-2 small text-muted text-end">
                                            {{ $doc->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Gráfica de Categorías
        const ctxCategorias = document.getElementById('chartCategorias').getContext('2d');
        new Chart(ctxCategorias, {
            type: 'bar',
            data: {
                labels: {!! json_encode($documentosPorCategoria->pluck('nombre')) !!},
                datasets: [{
                    label: 'Documentos',
                    data: {!! json_encode($documentosPorCategoria->pluck('documentos_activos_count')) !!},
                    backgroundColor: '#1e3a5f',
                    borderColor: '#f0a500',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfica de Años
        const ctxAnios = document.getElementById('chartAnios').getContext('2d');
        new Chart(ctxAnios, {
            type: 'line',
            data: {
                labels: {!! json_encode($documentosPorAnio->pluck('anio')) !!},
                datasets: [{
                    label: 'Documentos por Año',
                    data: {!! json_encode($documentosPorAnio->pluck('total')) !!},
                    backgroundColor: 'rgba(45, 106, 159, 0.2)',
                    borderColor: '#2d6a9f',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
