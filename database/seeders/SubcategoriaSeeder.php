<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategoria;
use App\Models\Categoria;

class SubcategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $anios = range(1967, 2025);

        $categorias = Categoria::all();

        foreach ($categorias as $cat) {
            foreach ($anios as $anio) {
                Subcategoria::create([
                    'categoria_id' => $cat->id,
                    'nombre'       => $anio,
                    'slug'         => $cat->slug . '-' . $anio,
                    'anio'         => $anio,
                    'activo'       => true,
                    'orden'        => $anio,
                ]);
            }
        }
    }
}