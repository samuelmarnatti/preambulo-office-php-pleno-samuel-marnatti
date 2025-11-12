<?php

namespace App\Jobs;

use App\Models\Locacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarNotificacaoEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(
        public Locacao $locacao,
        public string $tipo
    ) {}

    public function handle(): void
    {
        $cliente = $this->locacao->cliente;
        $filmes = $this->locacao->filmes->pluck('titulo')->join(', ');
        $dataDevolucao = $this->locacao->data_prevista_devolucao->format('d/m/Y');

        $assunto = match($this->tipo) {
            'lembrete' => 'Lembrete: Devolução de Filmes Amanhã',
            'devolucao' => 'Lembrete: Devolução de Filmes Hoje',
            'atraso' => 'URGENTE: Devolução de Filmes em Atraso',
            default => 'Notificação de Locação',
        };

        $mensagem = match($this->tipo) {
            'lembrete' => "Olá {$cliente->name},\n\nLembramos que a devolução dos filmes:\n{$filmes}\n\nEstá prevista para amanhã ({$dataDevolucao}).\n\nValor da locação: R$ " . number_format($this->locacao->valor_total, 2, ',', '.'),
            
            'devolucao' => "Olá {$cliente->name},\n\nA devolução dos filmes:\n{$filmes}\n\nEstá prevista para HOJE ({$dataDevolucao}).\n\nValor da locação: R$ " . number_format($this->locacao->valor_total, 2, ',', '.'),
            
            'atraso' => "Olá {$cliente->name},\n\nSua locação dos filmes:\n{$filmes}\n\nEstá em ATRASO desde {$dataDevolucao}.\n\nDias de atraso: {$this->locacao->dias_atraso}\nMulta acumulada: R$ " . number_format($this->locacao->multa, 2, ',', '.') . "\nValor total: R$ " . number_format($this->locacao->valor_total + $this->locacao->multa, 2, ',', '.'),
            
            default => "Notificação sobre sua locação.",
        };

        try {
            Mail::raw($mensagem, function ($message) use ($cliente, $assunto) {
                $message->to($cliente->email)
                    ->subject($assunto);
            });

            Log::info("Email enviado para {$cliente->email} - Tipo: {$this->tipo}");
        } catch (\Exception $e) {
            Log::error("Erro ao enviar email: " . $e->getMessage());
            throw $e;
        }
    }
}