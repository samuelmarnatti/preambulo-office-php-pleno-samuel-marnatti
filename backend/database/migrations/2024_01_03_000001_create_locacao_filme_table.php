<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locacao_filme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacao_id')->constrained('locacoes')->onDelete('cascade');
            $table->foreignId('filme_id')->constrained('filmes')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index('locacao_id');
            $table->index('filme_id');
            
            // Evitar duplicatas
            $table->unique(['locacao_id', 'filme_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locacao_filme');
    }
};