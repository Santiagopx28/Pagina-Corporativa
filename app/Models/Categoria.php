<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'icono',
        'descripcion',
        'activo',
        'orden'
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class)->orderBy('anio', 'desc');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}