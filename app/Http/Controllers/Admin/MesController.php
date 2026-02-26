<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mes;
use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MesController extends Controller
{
    // Propiedad privada para no repetir el array de meses en cada función
    private $mesesNombres = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    public function index()
    {
        $categorias = Categoria::activo()
            ->with(['subcategorias' => function($q) {
                $q->activo()
                    ->with(['meses' => function($q2) {
                        $q2->activo()->withCount('documentos'); // Para saber cuántos docs hay por mes
                    }])
                    ->withCount('meses'); // Para el badge de "X meses"
            }])
            ->orderBy('orden')
            ->get();

        return view('admin.meses.index', compact('categorias'));
    }

    public function create()
    {
        $subcategorias = Subcategoria::with('categoria')
            ->orderBy('anio', 'desc')
            ->get()
            ->groupBy('categoria.nombre');

        $mesesNombres = $this->mesesNombres;

        return view('admin.meses.create', compact('subcategorias', 'mesesNombres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'numero_mes'      => 'required|integer|min:1|max:12',
        ]);

        // MEJORA 1: Validar que el mes no exista ya en ese año (Subcategoría)
        $existe = Mes::where('subcategoria_id', $request->subcategoria_id)
            ->where('numero_mes', $request->numero_mes)
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Este mes ya ha sido agregado al año seleccionado.');
        }

        $subcategoria = Subcategoria::find($request->subcategoria_id);
        $nombreMes = $this->mesesNombres[$request->numero_mes];

        Mes::create([
            'subcategoria_id' => $request->subcategoria_id,
            'nombre'          => $nombreMes,
            // MEJORA 2: Slug limpio (Año + Mes)
            'slug'            => Str::slug($subcategoria->anio . '-' . $nombreMes . '-' . time()),
            'numero_mes'      => $request->numero_mes,
            'activo'          => true,
            'orden'           => $request->numero_mes,
        ]);

        return redirect()->route('admin.meses.index')
            ->with('success', 'Mes agregado exitosamente.');
    }

    public function edit(Mes $mes)
    {
        $subcategorias = Subcategoria::with('categoria')
            ->orderBy('anio', 'desc')
            ->get()
            ->groupBy('categoria.nombre');

        $mesesNombres = $this->mesesNombres;

        return view('admin.meses.edit', compact('mes', 'subcategorias', 'mesesNombres'));
    }

    public function update(Request $request, Mes $mes)
    {
        $request->validate([
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'numero_mes'      => 'required|integer|min:1|max:12',
            'activo'          => 'nullable',
        ]);

        // MEJORA 3: Validar duplicado al editar (excepto el registro actual)
        $existe = Mes::where('subcategoria_id', $request->subcategoria_id)
            ->where('numero_mes', $request->numero_mes)
            ->where('id', '!=', $mes->id)
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Ya existe este mes en el año seleccionado.');
        }

        $mes->update([
            'subcategoria_id' => $request->subcategoria_id,
            'nombre'          => $this->mesesNombres[$request->numero_mes],
            'numero_mes'      => $request->numero_mes,
            'activo'          => $request->has('activo'),
            'orden'           => $request->numero_mes,
        ]);

        return redirect()->route('admin.meses.index')
            ->with('success', 'Mes actualizado exitosamente.');
    }

    public function destroy(Mes $mes)
    {
        // MEJORA 4: Protección si el mes tiene documentos
        if ($mes->documentos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el mes porque contiene documentos asociados.');
        }

        $mes->delete();
        return redirect()->route('admin.meses.index')
            ->with('success', 'Mes eliminado exitosamente.');
    }
}