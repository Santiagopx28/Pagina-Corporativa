@extends('layouts.app')
@section('title', $categoria->nombre . ' — ' . $subcategoria->anio . ' — ' . $mes->nombre)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('portal.categoria', $categoria->slug) }}">{{ $categoria->nombre }}</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('portal.subcategoria', [$categoria->slug, $subcategoria->slug]) }}">{{ $subcategoria->anio }}</a>
    </li>
    <li class="breadcrumb-item active">{{ $mes->nombre }}</li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--ccv-primary);">
                📁 {{ $categoria->nombre }}
            </h4>
            <div class="d-flex gap-2 mt-1">
                <span class="badge" style="background:var(--ccv-accent);color:#333;font-size:.8rem;">
                    📅 {{ $subcategoria->anio }}
                </span>
                <span class="badge bg-info" style="font-size:.8rem;">
                    📆 {{ $mes->nombre }}
                </span>
            </div>
        </div>
        <a href="{{ route('portal.subcategoria', [$categoria->slug, $subcategoria->slug]) }}"
            class="btn btn-sm btn-outline-secondary">
            ← Volver al año
        </a>
    </div>

    {{-- Buscador local --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="input-group">
                    <input type="text" name="q" class="form-control"
                        placeholder="Buscar en {{ $mes->nombre }} {{ $subcategoria->anio }}..."
                        value="{{ request('q') }}">
                    <button class="btn text-white" type="submit" style="background:var(--ccv-primary);">
                        🔍 Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de documentos --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:var(--ccv-light);">
                        <tr>
                            <th class="px-4 py-3">Documento</th>
                            <th class="py-3">Número</th>
                            <th class="py-3">Fecha</th>
                            <th class="py-3">Tamaño</th>
                            <th class="py-3">Descargas</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td class="px-4 py-3" data-label="Documento">
                                    <div class="fw-semibold">{{ $doc->titulo }}</div>
                                    @if ($doc->descripcion)
                                        <div class="text-muted small">{{ Str::limit($doc->descripcion, 80) }}</div>
                                    @endif
                                </td>
                                <td class="py-3" data-label="Número">
                                    <span class="badge bg-secondary">{{ $doc->numero_documento ?? 'S/N' }}</span>
                                </td>
                                <td class="py-3 text-muted small" data-label="Fecha">
                                    {{ $doc->fecha_documento?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="py-3 text-muted small" data-label="Tamaño">
                                    {{ $doc->tamaño_formateado }}
                                </td>
                                <td class="py-3 text-muted small" data-label="Descargas">
                                    {{ $doc->descargas }}
                                </td>
                                <td class="py-3" data-label="Acciones">
                                    <div class="d-flex gap-1 flex-wrap">

                                        {{-- Botón Ver (modal preview) --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Vista previa"
                                            onclick="abrirPreview(
                                                '{{ Storage::url($doc->archivo_path) }}',
                                                '{{ addslashes($doc->titulo) }}',
                                                '{{ $doc->archivo_tipo }}'
                                            )">
                                            👁️ Ver
                                        </button>

                                        {{-- Botón Descargar --}}
                                        <a href="{{ route('portal.descargar', $doc->id) }}" class="btn btn-sm"
                                            style="background:var(--ccv-accent);color:#333;" title="Descargar documento">
                                            ⬇️
                                        </a>

                                        {{-- Botón Eliminar --}}
                                        <form action="{{ route('admin.documentos.destroy', $doc) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('⚠️ ¿Eliminar \"{{ addslashes($doc->titulo) }}\"?\n\nEsta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Eliminar documento">
                                                🗑️
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div style="font-size:2rem;">📭</div>
                                    No hay documentos para {{ $mes->nombre }} de {{ $subcategoria->anio }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $documentos->links() }}
    </div>


    {{-- ═══════════════════════════════════════
         MODAL DE PREVIEW DE DOCUMENTO
    ═══════════════════════════════════════ --}}
    <div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">

                {{-- Header --}}
                <div class="modal-header border-0 py-3 px-4" style="background:var(--ccv-primary);">
                    <div class="d-flex align-items-center gap-2 flex-grow-1 min-width-0">
                        <span style="font-size:1.2rem;">📄</span>
                        <h6 class="modal-title text-white fw-semibold mb-0 text-truncate" id="modalPreviewLabel">
                            Vista previa
                        </h6>
                    </div>
                    <div class="d-flex gap-2 ms-3">
                        <a href="#" id="btnDescargarModal" class="btn btn-sm"
                            style="background:var(--ccv-accent);color:#333;font-weight:600;" target="_blank">
                            ⬇️ Descargar
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal">✕</button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-body p-0 position-relative" style="height:78vh;">

                    {{-- Spinner de carga --}}
                    <div id="previewSpinner" class="position-absolute top-50 start-50 translate-middle text-center"
                        style="z-index:10;">
                        <div class="spinner-border" style="color:var(--ccv-secondary);width:3rem;height:3rem;"
                            role="status"></div>
                        <div class="mt-2 text-muted small">Cargando documento...</div>
                    </div>

                    {{-- iframe para PDFs --}}
                    <iframe id="previewIframe" src="" style="width:100%;height:100%;border:none;display:none;"
                        onload="ocultarSpinner()">
                    </iframe>

                    {{-- Mensaje para tipos no previsualizable --}}
                    <div id="previewNoDisponible" class="position-absolute top-50 start-50 translate-middle text-center"
                        style="display:none;">
                        <div style="font-size:3rem;">📎</div>
                        <p class="text-muted mt-2">
                            Este tipo de archivo no puede previsualizarse directamente.<br>
                            Puedes descargarlo usando el botón de arriba.
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Tipos de archivo que admiten preview en iframe
        const TIPOS_PREVIEW = ['pdf', 'doc', 'docx'];

        function abrirPreview(url, titulo, tipo) {
            // Actualizar título del modal
            document.getElementById('modalPreviewLabel').textContent = titulo;

            // Resetear estado
            const iframe = document.getElementById('previewIframe');
            const spinner = document.getElementById('previewSpinner');
            const noDisp = document.getElementById('previewNoDisponible');

            iframe.style.display = 'none';
            noDisp.style.display = 'none';
            spinner.style.display = 'block';
            iframe.src = '';

            // Determinar si se puede previsualizar
            const tipoLower = (tipo || '').toLowerCase();
            const puedePreview = TIPOS_PREVIEW.includes(tipoLower);

            if (puedePreview) {
                const tiposGoogle = ['doc', 'docx'];
                if (tiposGoogle.includes(tipoLower)) {
                    // Visor de Google Docs para archivos Word
                    iframe.src = 'https://docs.google.com/viewer?url=' + encodeURIComponent(window.location.origin + url) +
                        '&embedded=true';
                } else {
                    iframe.src = url;
                }
            }

            // Abrir el modal
            const modal = new bootstrap.Modal(document.getElementById('modalPreview'));
            modal.show();
        }

        function ocultarSpinner() {
            document.getElementById('previewSpinner').style.display = 'none';
            document.getElementById('previewIframe').style.display = 'block';
        }

        // Limpiar iframe al cerrar el modal (evita que siga cargando en background)
        document.getElementById('modalPreview').addEventListener('hidden.bs.modal', function() {
            const iframe = document.getElementById('previewIframe');
            iframe.src = '';
            iframe.style.display = 'none';
            document.getElementById('previewSpinner').style.display = 'block';
            document.getElementById('previewNoDisponible').style.display = 'none';
        });
    </script>
@endpush
