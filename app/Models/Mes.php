<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    protected $table = 'meses';

    protected $fillable = [
        'subcategoria_id',
        'nombre',
        'slug',
        'numero_mes',
        'activo',
        'orden'
    ];

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function scopeActivo($query) {
        return $query->where('activo', true);
    }   
}