<?php

namespace Tests\Feature;

use App\Models\Filme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheFilmesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // Limpar cache antes de cada teste
    }

    /**
     * ✅ TESTE OBRIGATÓRIO 4: Cache invalidado após atualização de filme
     */
    public function test_cache_invalidado_apos_atualizacao_filme(): void
    {
        // Arrange
        $filme = Filme::factory()->create(['titulo' => 'Título Original']);

        // Carregar cache
        Filme::getCatalogo();
        $this->assertTrue(Cache::has('filmes.catalogo'));

        // Act - Atualizar filme
        $filme->update(['titulo' => 'Título Atualizado']);

        // Assert - Cache foi invalidado
        $this->assertFalse(Cache::has('filmes.catalogo'));

        // Recarregar e verificar se dados foram atualizados
        $catalogoNovo = Filme::getCatalogo();
        $filmeAtualizado = $catalogoNovo->firstWhere('id', $filme->id);
        $this->assertEquals('Título Atualizado', $filmeAtualizado->titulo);
    }

    /**
     * Teste: Cache é invalidado ao criar filme
     */
    public function test_cache_invalidado_ao_criar_filme(): void
    {
        // Arrange
        Filme::factory()->count(5)->create();

        // Carregar cache
        $catalogoInicial = Filme::getCatalogo();
        $this->assertEquals(5, $catalogoInicial->count());
        $this->assertTrue(Cache::has('filmes.catalogo'));

        // Act - Criar novo filme
        Filme::create([
            'titulo' => 'Novo Filme',
            'sinopse' => 'Sinopse teste',
            'ano' => 2024,
            'categoria' => 'Ação',
            'valor_locacao' => 15.00,
            'quantidade_disponivel' => 3,
        ]);

        // Assert - Cache foi invalidado e atualizado
        $catalogoAtualizado = Filme::getCatalogo();
        $this->assertEquals(6, $catalogoAtualizado->count());
    }

    /**
     * Teste: Cache é invalidado ao deletar filme
     */
    public function test_cache_invalidado_ao_deletar_filme(): void
    {
        // Arrange
        $filme = Filme::factory()->create();

        Filme::getCatalogo();
        $this->assertTrue(Cache::has('filmes.catalogo'));

        // Act
        $filme->delete();

        // Assert
        $this->assertFalse(Cache::has('filmes.catalogo'));
    }

    /**
     * Teste: Cache-aside pattern funciona corretamente
     */
    public function test_cache_aside_pattern_funcionando(): void
    {
        // Arrange
        Filme::factory()->count(3)->create();

        // Act - Primeira chamada (cache MISS)
        $this->assertFalse(Cache::has('filmes.catalogo'));
        $catalogo1 = Filme::getCatalogo();

        // Assert - Cache foi criado
        $this->assertTrue(Cache::has('filmes.catalogo'));

        // Act - Segunda chamada (cache HIT)
        $catalogo2 = Filme::getCatalogo();

        // Assert - Retornou mesmos dados do cache
        $this->assertEquals($catalogo1->pluck('id')->toArray(), $catalogo2->pluck('id')->toArray());
    }

    /**
     * Teste: Cache de filmes disponíveis funciona independentemente
     */
    public function test_cache_disponiveis_independente(): void
    {
        // Arrange
        Filme::factory()->disponivel()->count(3)->create();
        Filme::factory()->indisponivel()->count(2)->create();

        // Act
        $disponiveis = Filme::getDisponiveis();

        // Assert
        $this->assertEquals(3, $disponiveis->count());
        $this->assertTrue(Cache::has('filmes.disponiveis'));
        
        // Todos devem ter quantidade > 0
        $disponiveis->each(function ($filme) {
            $this->assertGreaterThan(0, $filme->quantidade_disponivel);
        });
    }
}