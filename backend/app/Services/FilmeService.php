<?php

namespace App\Services;

use App\Models\Filme;
use Illuminate\Support\Facades\Cache;

class FilmeService
{
    /**
     * Criar filme
     */
    public function criar(array $dados): Filme
    {
        return Filme::create($dados);
    }


    public function atualizar(Filme $filme, array $dados): Filme
    {
        $filme->update($dados);
        return $filme->fresh();
    }


    public function deletar(Filme $filme): bool
    {
        // Verificar se filme tem locações ativas
        if ($filme->locacoes()->pendentes()->exists()) {
            throw new \Exception('Não é possível deletar um filme com locações ativas.');
        }

        return $filme->delete();
    }

    /**
     * Obter catálogo (com cache)
     */
    public function getCatalogo()
    {
        return Filme::getCatalogo();
    }

    public function getDisponiveis()
    {
        return Filme::getDisponiveis();
    }


    public function buscarPorCategoria(string $categoria)
    {
        return Filme::porCategoria($categoria)->get();
    }

    public function limparCache(): void
    {
        Cache::forget('filmes.catalogo');
        Cache::forget('filmes.disponiveis');
    }
}
