<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Filme;
use App\Models\Locacao;
use App\Services\LocacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocacaoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ TESTE OBRIGATÓRIO 1: Cliente com devolução pendente não pode alugar
     */
    public function test_cliente_com_devolucao_pendente_nao_pode_alugar(): void
    {
        // Arrange
        $cliente = User::factory()->cliente()->create();
        $filme1 = Filme::factory()->disponivel()->create();
        $filme2 = Filme::factory()->disponivel()->create();

        // Criar locação ativa (pendente)
        $locacaoAtiva = Locacao::factory()->ativa()->create([
            'user_id' => $cliente->id,
        ]);
        $locacaoAtiva->filmes()->attach($filme1->id);

        $service = new LocacaoService();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cliente possui devoluções pendentes e não pode alugar novos filmes.');

        $service->criar($cliente, [$filme2->id]);
    }

    /**
     * Teste: Cliente sem pendências pode alugar
     */
    public function test_cliente_sem_pendencias_pode_alugar(): void
    {
        // Arrange
        $cliente = User::factory()->cliente()->create();
        $filme = Filme::factory()->disponivel()->create(['quantidade_disponivel' => 5]);

        $service = new LocacaoService();

        // Act
        $locacao = $service->criar($cliente, [$filme->id]);

        // Assert
        $this->assertDatabaseHas('locacoes', [
            'user_id' => $cliente->id,
            'status' => 'ativo',
        ]);

        $this->assertEquals(1, $locacao->filmes->count());
        $this->assertEquals($filme->valor_locacao, $locacao->valor_total);
    }

    /**
     * ✅ TESTE OBRIGATÓRIO 3: Fluxo completo de locação → devolução → estoque
     */
    public function test_fluxo_completo_locacao_devolucao_estoque(): void
    {
        // Arrange
        $cliente = User::factory()->cliente()->create();
        $filme = Filme::factory()->create(['quantidade_disponivel' => 5]);

        $estoqueInicial = $filme->quantidade_disponivel;
        $service = new LocacaoService();

        // Act - Criar locação
        $locacao = $service->criar($cliente, [$filme->id]);

        // Assert - Estoque decrementado
        $filme->refresh();
        $this->assertEquals($estoqueInicial - 1, $filme->quantidade_disponivel);
        $this->assertEquals('ativo', $locacao->status);

        // Act - Devolver locação
        $service->devolver($locacao);

        // Assert - Estoque retornou ao normal
        $filme->refresh();
        $locacao->refresh();
        
        $this->assertEquals($estoqueInicial, $filme->quantidade_disponivel);
        $this->assertEquals('devolvido', $locacao->status);
        $this->assertNotNull($locacao->data_devolucao);
    }

    /**
     * Teste: Não é possível alugar filme indisponível
     */
    public function test_nao_pode_alugar_filme_indisponivel(): void
    {
        // Arrange
        $cliente = User::factory()->cliente()->create();
        $filme = Filme::factory()->indisponivel()->create(['quantidade_disponivel' => 0]);

        $service = new LocacaoService();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("não está disponível");

        $service->criar($cliente, [$filme->id]);
    }

    /**
     * Teste: Valor total calculado corretamente com múltiplos filmes
     */
    public function test_valor_total_multiplos_filmes(): void
    {
        // Arrange
        $cliente = User::factory()->cliente()->create();
        $filme1 = Filme::factory()->disponivel()->create(['valor_locacao' => 10.00]);
        $filme2 = Filme::factory()->disponivel()->create(['valor_locacao' => 15.00]);
        $filme3 = Filme::factory()->disponivel()->create(['valor_locacao' => 20.00]);

        $service = new LocacaoService();

        // Act
        $locacao = $service->criar($cliente, [$filme1->id, $filme2->id, $filme3->id]);

        // Assert
        $this->assertEquals(45.00, $locacao->valor_total);
        $this->assertEquals(3, $locacao->filmes->count());
    }

    /**
     * Teste: Não é possível devolver locação já devolvida
     */
    public function test_nao_pode_devolver_locacao_ja_devolvida(): void
    {
        // Arrange
        $cliente = User::factory()->cliente()->create();
        $filme = Filme::factory()->disponivel()->create();
        
        $service = new LocacaoService();
        $locacao = $service->criar($cliente, [$filme->id]);
        $service->devolver($locacao);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('já foi devolvida');

        $service->devolver($locacao);
    }
}