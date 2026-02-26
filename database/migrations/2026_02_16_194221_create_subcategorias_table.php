<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcategorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->integer('anio');
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Agregar subcategoria_id a documentos
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['subcategoria_id']);
            $table->dropColumn('subcategoria_id');
        });
        Schema::dropIfExists('subcategorias');
    }
};