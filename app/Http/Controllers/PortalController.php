<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Mes;
use App\Models\Documento;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        // Cargamos categorías activas con sus hijos filtrados por sus respectivos scopes
        $categorias = Categoria::activo()
            ->orderBy('orden')
            ->with(['subcategorias' => function($q) {
                $q->activo()
                  ->withCount(['documentos as documentos_count' => function($query) {
                      $query->activo();
                  }])
                  ->with(['meses' => function($q2) {
                      $q2->activo()->withCount(['documentos as documentos_count' => function($query) {
                          $query->activo();
                      }]);
                  }]);
            }])
            ->withCount(['documentos as documentos_count' => function($query) {
                $query->activo();
            }])
            ->get();

        $recientes = Documento::activo()
            ->with('categoria', 'subcategoria', 'mes')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $totalDocumentos = Documento::activo()->count();

        return view('portal.index', compact('categorias', 'recientes', 'totalDocumentos'));
    }

    public function categoria(Categoria $categoria)
    {
        // Verificamos que la categoría esté activa antes de mostrarla
        if (!$categoria->activo) abort(404);

        $subcategorias = $categoria->subcategorias()
            ->activo()
            ->withCount(['documentos as documentos_count' => function($query) {
                $query->activo();
            }])
            ->get();

        $documentos = $categoria->documentos()
            ->activo()
            ->orderBy('fecha_documento', 'desc')
            ->paginate(5);

        return view('portal.categoria', compact('categoria', 'subcategorias', 'documentos'));
    }

    public function subcategoria(Categoria $categoria, Subcategoria $subcategoria)
    {
        if (!$subcategoria->activo) abort(404);

        $meses = $subcategoria->meses()
            ->activo()
            ->withCount(['documentos as documentos_count' => function($query) {
                $query->activo();
            }])
            ->get();

        $documentos = $subcategoria->documentos()
            ->activo()
            ->with('mes')
            ->orderBy('fecha_documento', 'desc')
            ->paginate(5);

        return view('portal.subcategoria', compact('categoria', 'subcategoria', 'meses', 'documentos'));
    }

    public function mes(Categoria $categoria, Subcategoria $subcategoria, Mes $mes)
    {
        if (!$mes->activo) abort(404);

        $documentos = $mes->documentos()
            ->activo()
            ->orderBy('fecha_documento', 'desc')
            ->paginate(5);

        return view('portal.mes', compact('categoria', 'subcategoria', 'mes', 'documentos'));
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q');

        $documentos = Documento::activo()
            ->when($q, function($query) use ($q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('titulo', 'like', "%{$q}%")
                        ->orWhere('numero_documento', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->when($request->categoria, fn($qb) => $qb->where('categoria_id', $request->categoria))
            ->with('categoria', 'subcategoria')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $categorias = Categoria::activo()->get();

        return view('portal.buscar', compact('documentos', 'categorias', 'q'));
    }

    public function descargar(Documento $documento)
    {
        $documento->increment('descargas');
        return response()->download(
            storage_path('app/public/' . $documento->archivo_path),
            $documento->archivo_nombre
        );
    }
}