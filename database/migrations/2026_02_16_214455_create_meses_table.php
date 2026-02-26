<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategoria_id')->constrained('subcategorias')->onDelete('cascade');
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->integer('numero_mes'); // 1-12
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Agregar mes_id a documentos
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('mes_id')->nullable()->constrained('meses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['mes_id']);
            $table->dropColumn('mes_id');
        });
        Schema::dropIfExists('meses');
    }
};