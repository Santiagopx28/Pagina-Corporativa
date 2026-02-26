<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $fillable = [
        'categoria_id',
        'nombre',
        'slug',
        'anio',
        'activo',
        'orden'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function documentosActivos()
    {
        return $this->hasMany(Documento::class)->where('estado', 'activo');
    }

    public function meses()
    {
        return $this->hasMany(Mes::class)->orderBy('numero_mes');
    }

    public function scopeActivo($query){
        return $query->where('activo', true);
    }
}