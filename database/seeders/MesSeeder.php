<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mes;
use App\Models\Subcategoria;
use Illuminate\Support\Facades\DB;

class MesSeeder extends Seeder
{
    public function run(): void
    {
        $meses = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $subcategorias = Subcategoria::all();
        $data = [];
        $now = now();

        foreach ($subcategorias as $sub) {
            foreach ($meses as $num => $nombre) {
                $data[] = [
                    'subcategoria_id' => $sub->id,
                    'nombre'          => $nombre,
                    'slug'            => $sub->slug . '-' . strtolower($nombre) . '-' . $sub->id . '-' . $num,
                    'numero_mes'      => $num,
                    'activo'          => true,
                    'orden'           => $num,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        // Insertar en lotes de 500
        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('meses')->insert($chunk);
        }

        $this->command->info('✅ Meses creados exitosamente');
    }
}