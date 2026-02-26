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
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('numero_documento')->nullable();
            $table->date('fecha_documento')->nullable();
            $table->string('archivo_path');
            $table->string('archivo_nombre');
            $table->string('archivo_tipo');
            $table->bigInteger('archivo_tamaño')->default(0);
            $table->enum('estado', ['activo','inactivo','archivado'])->default('activo');
            $table->integer('descargas')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};