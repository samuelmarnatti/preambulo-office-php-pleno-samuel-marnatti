<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filmes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('sinopse');
            $table->integer('ano');
            $table->string('categoria', 50);
            $table->decimal('valor_locacao', 8, 2);
            $table->integer('quantidade_disponivel')->default(0);
            $table->timestamps();

            // Indexes para performance
            $table->index('categoria');
            $table->index('ano');
            $table->index(['quantidade_disponivel', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
};