<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();

            // Relación con documentos
            $table->unsignedBigInteger('documento_id');
            $table->unsignedInteger('chunk_index')->default(0);

            // Contenido del chunk
            $table->longText('texto');

            // Embedding (lo guardamos en JSON por ahora)
            $table->json('embedding')->nullable();

            // Metadata opcional
            $table->string('embedding_model')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['documento_id', 'chunk_index']);
            $table->foreign('documento_id')->references('id')->on('documentos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
