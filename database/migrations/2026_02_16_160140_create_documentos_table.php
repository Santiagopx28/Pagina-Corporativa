<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();

            // Sin foreign keys (evita error 121). Mantiene la estructura que el portal necesita.
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('user_id');

            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('numero_documento')->nullable();
            $table->date('fecha_documento')->nullable();

            $table->string('archivo_path');
            $table->string('archivo_nombre');
            $table->string('archivo_tipo');
            $table->bigInteger('archivo_tamaño')->default(0);

            $table->enum('estado', ['activo', 'inactivo', 'archivado'])->default('activo');
            $table->integer('descargas')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Índices útiles
            $table->index('categoria_id');
            $table->index('user_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
