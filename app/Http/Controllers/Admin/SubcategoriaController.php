<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoriaController extends Controller
{
    public function index()
    {
        // Aplicamos el scope activo que creamos antes para las categorías
        $categorias = Categoria::activo() 
            ->orderBy('orden')
            ->with(['subcategorias' => function($q) {
                $q->orderBy('anio', 'desc');
            }])
            ->get();

        return view('admin.subcategorias.index', compact('categorias'));
    }

    public function create()
    {
        $categorias = Categoria::activo()->orderBy('nombre')->get();
        return view('admin.subcategorias.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'anio'         => 'required|integer|min:1967|max:2099',
        ]);

        // MEJORA 1: Evitar que creen dos veces el mismo año para la misma categoría
        $existe = Subcategoria::where('categoria_id', $request->categoria_id)
            ->where('anio', $request->anio)
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Este año ya existe dentro de esta categoría.');
        }

        $categoria = Categoria::find($request->categoria_id);

        Subcategoria::create([
            'categoria_id' => $request->categoria_id,
            'nombre'       => $request->anio, // Nombre es el año
            // MEJORA 2: Slug más profesional (Categoría + Año)
            'slug'         => Str::slug($categoria->nombre . '-' . $request->anio),
            'anio'         => $request->anio,
            'activo'       => true,
            'orden'        => $request->anio,
        ]);

        return redirect()->route('admin.subcategorias.index')
            ->with('success', 'Año agregado exitosamente.');
    }

    public function edit(Subcategoria $subcategoria)
    {
        $categorias = Categoria::activo()->orderBy('nombre')->get();
        return view('admin.subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'anio'         => 'required|integer|min:1967|max:2099',
            'activo'       => 'nullable', // Cambiado a nullable para manejo de checkbox
        ]);

        // MEJORA 3: Validar duplicado al editar (excepto ella misma)
        $existe = Subcategoria::where('categoria_id', $request->categoria_id)
            ->where('anio', $request->anio)
            ->where('id', '!=', $subcategoria->id)
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Ya existe otra subcategoría con este año en esta categoría.');
        }

        $subcategoria->update([
            'categoria_id' => $request->categoria_id,
            'nombre'       => $request->anio,
            'anio'         => $request->anio,
            'activo'       => $request->has('activo'), // Captura el checkbox correctamente
            'orden'        => $request->anio,
        ]);

        return redirect()->route('admin.subcategorias.index')
            ->with('success', 'Año actualizado exitosamente.');
    }

    public function destroy(Subcategoria $subcategoria)
    {
        // MEJORA 4: Protección de integridad
        if ($subcategoria->meses()->count() > 0 || $subcategoria->documentos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: Este año tiene meses o documentos asociados.');
        }

        $subcategoria->delete();
        return redirect()->route('admin.subcategorias.index')
            ->with('success', 'Año eliminado exitosamente.');
    }
}