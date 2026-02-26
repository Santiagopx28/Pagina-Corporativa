<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $documentos = Documento::with('categoria', 'autor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.documentos.index', compact('documentos'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        return view('admin.documentos.create', compact('categorias'));
    }

    public function store(Request $request)
{
    $request->validate([
        'titulo'          => 'required|max:255',
        'categoria_id'    => 'required|exists:categorias,id',
        'subcategoria_id' => 'required|exists:subcategorias,id',
        'mes_id'          => 'required|exists:meses,id',
        'archivos'        => 'required|array|min:1',
        'archivos.*'      => 'file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        'fecha_documento' => 'nullable|date',
        'numero_documento'=> 'nullable|max:100',
        'descripcion'     => 'nullable|max:1000',
    ]);

    foreach ($request->file('archivos') as $archivo) {
        $path = $archivo->store('documentos', 'public');

        Documento::create([
            'titulo'           => $request->titulo,
            'slug'             => Str::slug($request->titulo) . '-' . time() . '-' . uniqid(),
            'categoria_id'     => $request->categoria_id,
            'subcategoria_id'  => $request->subcategoria_id,
            'mes_id'           => $request->mes_id,
            'user_id'          => auth()->id(),
            'descripcion'      => $request->descripcion,
            'numero_documento' => $request->numero_documento,
            'fecha_documento'  => $request->fecha_documento,
            'archivo_path'     => $path,
            'archivo_nombre'   => $archivo->getClientOriginalName(),
            'archivo_tipo'     => $archivo->getClientOriginalExtension(),
            'archivo_tamaño'   => $archivo->getSize(),
            'estado'           => 'activo',
        ]);
    }

    $total = count($request->file('archivos'));

    return redirect()->route('admin.documentos.index')
        ->with('success', $total === 1
            ? 'Documento subido exitosamente.'
            : "{$total} documentos subidos exitosamente.");
}

    public function edit(Documento $documento)
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        return view('admin.documentos.edit', compact('documento', 'categorias'));
    }

    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'titulo'          => 'required|max:255',
            'categoria_id'    => 'required|exists:categorias,id',
            'archivo'         => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
            'fecha_documento' => 'nullable|date',
            'numero_documento'=> 'nullable|max:100',
            'descripcion'     => 'nullable|max:1000',
            'estado'          => 'required|in:activo,inactivo,archivado',
        ]);

        $data = $request->only([
            'titulo', 'categoria_id', 'descripcion',
            'numero_documento', 'fecha_documento', 'estado'
        ]);

        if ($request->hasFile('archivo')) {
            Storage::disk('public')->delete($documento->archivo_path);
            $archivo = $request->file('archivo');
            $data['archivo_path']   = $archivo->store('documentos', 'public');
            $data['archivo_nombre'] = $archivo->getClientOriginalName();
            $data['archivo_tipo']   = $archivo->getClientOriginalExtension();
            $data['archivo_tamaño'] = $archivo->getSize();
        }

        $documento->update($data);

        return redirect()->route('admin.documentos.index')
            ->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Documento $documento)
    {
        Storage::disk('public')->delete($documento->archivo_path);
        $documento->delete();

        return redirect()->route('admin.documentos.index')
            ->with('success', 'Documento eliminado.');
    }
}