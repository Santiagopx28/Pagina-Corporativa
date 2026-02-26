<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalDocumentos = Documento::where('estado', 'activo')->count();
        $totalCategorias = Categoria::where('activo', true)->count();
        $totalDescargas  = Documento::sum('descargas');
        $documentosHoy   = Documento::whereDate('created_at', today())->count();

        // Documentos por categoría - CORREGIDO
        $documentosPorCategoria = Categoria::where('activo', true)
            ->withCount(['documentos as documentos_count' => function($query) {
                $query->where('estado', 'activo');
            }])
            ->orderBy('documentos_count', 'desc')
            ->get();

        // Documentos por año
        $documentosPorAnio = Subcategoria::select('subcategorias.anio', DB::raw('count(*) as total'))
            ->join('documentos', 'subcategorias.id', '=', 'documentos.subcategoria_id')
            ->where('documentos.estado', 'activo')
            ->groupBy('subcategorias.anio')
            ->orderBy('subcategorias.anio', 'desc')
            ->limit(10)
            ->get();

        // Top 10 documentos más descargados
        $topDescargados = Documento::where('estado', 'activo')
            ->with('categoria')
            ->orderBy('descargas', 'desc')
            ->limit(10)
            ->get();

        // Actividad reciente (últimos 10 documentos subidos)
        $recientes = Documento::with('categoria', 'autor')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalDocumentos',
            'totalCategorias',
            'totalDescargas',
            'documentosHoy',
            'documentosPorCategoria',
            'documentosPorAnio',
            'topDescargados',
            'recientes'
        ));
    }
}