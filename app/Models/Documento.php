<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'categoria_id',
        'user_id',
        'titulo',
        'slug',
        'descripcion',
        'numero_documento',
        'fecha_documento',
        'archivo_path',
        'archivo_nombre',
        'archivo_tipo',
        'archivo_tamano',
        'estado',
        'descargas',
        'subcategoria_id',
        'mes_id',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTamanoFormateadoAttribute(): string
    {
        $bytes = $this->archivo_tamaño;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function mes()
    {
        return $this->belongsTo(Mes::class);
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }
}