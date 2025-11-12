<?php

namespace App\Services;

use App\Models\Locacao;
use App\Models\User;
use App\Models\Filme;
use App\Jobs\EnviarNotificacaoEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LocacaoService
{
    /**
     * Criar nova locação
     */
    public function criar(User $cliente, array $filmesIds, int $diasLocacao = 7): Locacao
    {
        // Validar se cliente pode alugar
        if (!$cliente->podeAlugar()) {
            throw new \Exception('Cliente possui devoluções pendentes e não pode alugar novos filmes.');
        }

        // Buscar filmes
        $filmes = Filme::findMany($filmesIds);

        // Validar disponibilidade
        foreach ($filmes as $filme) {
            if (!$filme->estaDisponivel()) {
                throw new \Exception("O filme '{$filme->titulo}' não está disponível no momento.");
            }
        }

        return DB::transaction(function () use ($cliente, $filmes, $diasLocacao) {
            // Calcular valor total
            $valorTotal = $filmes->sum('valor_locacao');

            // Criar locação
            $locacao = Locacao::create([
                'user_id' => $cliente->id,
                'data_inicio' => now(),
                'data_prevista_devolucao' => now()->addDays($diasLocacao),
                'status' => 'ativo',
                'valor_total' => $valorTotal,
                'multa' => 0,
            ]);

            // Associar filmes
            $locacao->filmes()->attach($filmes->pluck('id'));

            // Atualizar estoque
            foreach ($filmes as $filme) {
                $filme->decrementarEstoque();
            }

            // Agendar notificações
            $this->agendarNotificacoes($locacao);

            return $locacao->load('filmes');
        });
    }

    /**
     * Devolver locação
     */
    public function devolver(Locacao $locacao): Locacao
    {
        if ($locacao->foiDevolvida()) {
            throw new \Exception('Esta locação já foi devolvida.');
        }

        return DB::transaction(function () use ($locacao) {
            // Calcular multa
            $multa = $locacao->calcularMulta();

            // Atualizar locação
            $locacao->update([
                'data_devolucao' => now(),
                'status' => 'devolvido',
                'multa' => $multa,
            ]);

            // Retornar filmes ao estoque
            foreach ($locacao->filmes as $filme) {
                $filme->incrementarEstoque();
            }

            return $locacao->fresh();
        });
    }

    /**
     * Agendar notificações de lembretes
     */
    private function agendarNotificacoes(Locacao $locacao): void
    {
        // Notificação 1 dia antes
        $dataLembrete = $locacao->data_prevista_devolucao->copy()->subDay();
        if ($dataLembrete->isFuture()) {
            EnviarNotificacaoEmail::dispatch($locacao, 'lembrete')
                ->delay($dataLembrete->setTime(9, 0));
        }

        // Notificação no dia da devolução
        $dataDevolucao = $locacao->data_prevista_devolucao->copy();
        if ($dataDevolucao->isFuture()) {
            EnviarNotificacaoEmail::dispatch($locacao, 'devolucao')
                ->delay($dataDevolucao->setTime(9, 0));
        }
    }

    /**
     * Processar locações atrasadas (executar diariamente via scheduler)
     */
    public function processarAtrasadas(): int
    {
        $locacoesAtrasadas = Locacao::ativas()
            ->where('data_prevista_devolucao', '<', now())
            ->get();

        foreach ($locacoesAtrasadas as $locacao) {
            $locacao->update([
                'status' => 'atrasado',
                'multa' => $locacao->calcularMulta(),
            ]);

            // Enviar notificação de atraso
            EnviarNotificacaoEmail::dispatch($locacao, 'atraso');
        }

        return $locacoesAtrasadas->count();
    }

    /**
     * Obter locações com devolução prevista para hoje (Admin)
     */
    public function getDevolucoesPrevistaHoje()
    {
        return Locacao::with(['cliente', 'filmes'])
            ->comDevolucaoHoje()
            ->get();
    }
}