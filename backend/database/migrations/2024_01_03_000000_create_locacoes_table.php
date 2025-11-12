<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_prevista_devolucao');
            $table->date('data_devolucao')->nullable();
            $table->enum('status', ['ativo', 'devolvido', 'atrasado'])->default('ativo');
            $table->decimal('valor_total', 10, 2);
            $table->decimal('multa', 10, 2)->default(0);
            $table->timestamps();

            // Indexes para consultas frequentes
            $table->index('user_id');
            $table->index('status');
            $table->index('data_prevista_devolucao');
            $table->index(['status', 'user_id']); // Verificar pendências do cliente
            $table->index(['data_prevista_devolucao', 'status']); // Relatórios admin
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locacoes');
    }
};