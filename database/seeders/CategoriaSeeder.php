<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre'      => 'Actas J. Directiva',
                'slug'        => 'actas-junta-directiva',
                'icono'       => 'clipboard',
                'descripcion' => 'Actas oficiales de la Junta Directiva',
                'orden'       => 1
            ],
            [
                'nombre'      => 'Resolución J. Directiva',
                'slug'        => 'resolucion-junta-directiva',
                'icono'       => 'document-check',
                'descripcion' => 'Resoluciones emitidas por la Junta Directiva',
                'orden'       => 2
            ],
            [
                'nombre'      => 'Resolución Presidencia',
                'slug'        => 'resolucion-presidencia',
                'icono'       => 'document-check',
                'descripcion' => 'Resoluciones emitidas por la Presidencia',
                'orden'       => 3
            ],
            [
                'nombre'      => 'Manuales Internos',
                'slug'        => 'manuales-internos',
                'icono'       => 'book-open',
                'descripcion' => 'Manuales de procedimientos internos',
                'orden'       => 4
            ],
            [
                'nombre'      => 'Resolución Especial',
                'slug'        => 'resolucion-especial',
                'icono'       => 'star',
                'descripcion' => 'Resoluciones de carácter especial',
                'orden'       => 5
            ],
            [
                'nombre'      => 'Estatutos',
                'slug'        => 'estatutos',
                'icono'       => 'shield-check',
                'descripcion' => 'Estatutos y reglamentos institucionales',
                'orden'       => 6
            ],
            [
                'nombre'      => 'Documentos Importantes',
                'slug'        => 'documentos-importantes',
                'icono'       => 'star',
                'descripcion' => 'Documentos de relevancia institucional',
                'orden'       => 7
            ],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }
    }
}