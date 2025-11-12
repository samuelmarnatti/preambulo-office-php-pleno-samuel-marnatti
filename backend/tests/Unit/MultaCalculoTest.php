<?php

namespace Tests\Unit;

use App\Models\Locacao;
use App\Models\Filme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class MultaCalculoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ TESTE OBRIGATÓRIO 2: Cálculo correto da multa diária de atraso
     */
    public function test_calculo_correto_multa_por_dia(): void
    {
        // Arrange
        Carbon::setTestNow('2025-01-15 10:00:00');

        $locacao = Locacao::factory()->create([
            'data_prevista_devolucao' => Carbon::parse('2025-01-10'), // 5 dias atrás
            'status' => 'atrasado',
        ]);

        $locacao->filmes()->attach(Filme::factory()->create());

        // Act
        $multa = $locacao->calcularMulta();

        // Assert
        // 5 dias * R$ 5,00 * 1 filme = R$ 25,00
        $this->assertEquals(25.00, $multa);
    }

    /**
     * Teste: Sem atraso, multa é zero
     */
    public function test_sem_atraso_multa_zero(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create([
            'data_prevista_devolucao' => now()->addDays(2),
            'status' => 'ativo',
        ]);

        $locacao->filmes()->attach(Filme::factory()->create());

        // Act
        $multa = $locacao->calcularMulta();

        // Assert
        $this->assertEquals(0, $multa);
    }

    /**
     * Teste: Multa com 1 dia de atraso e 1 filme
     */
    public function test_multa_1_dia_1_filme(): void
    {
        // Arrange
        Carbon::setTestNow('2025-01-11 10:00:00');

        $locacao = Locacao::factory()->create([
            'data_prevista_devolucao' => Carbon::parse('2025-01-10'), // 1 dia atrás
            'status' => 'atrasado',
        ]);

        $locacao->filmes()->attach(Filme::factory()->create());

        // Act
        $multa = $locacao->calcularMulta();

        // Assert
        // 1 dia * R$ 5,00 * 1 filme = R$ 5,00
        $this->assertEquals(5.00, $multa);
    }

    /**
     * Teste: Multa com múltiplos dias e múltiplos filmes
     */
    public function test_multa_multiplos_dias_multiplos_filmes(): void
    {
        // Arrange
        Carbon::setTestNow('2025-01-18 10:00:00');

        $locacao = Locacao::factory()->create([
            'data_prevista_devolucao' => Carbon::parse('2025-01-10'), // 8 dias atrás
            'status' => 'atrasado',
        ]);

        // 3 filmes
        $locacao->filmes()->attach([
            Filme::factory()->create()->id,
            Filme::factory()->create()->id,
            Filme::factory()->create()->id,
        ]);

        // Act
        $multa = $locacao->calcularMulta();

        // Assert
        // 8 dias * R$ 5,00 * 3 filmes = R$ 120,00
        $this->assertEquals(120.00, $multa);
    }

    /**
     * Teste: Atributo dias_atraso calculado corretamente
     */
    public function test_dias_atraso_attribute(): void
    {
        // Arrange
        Carbon::setTestNow('2025-01-20 10:00:00');

        $locacao = Locacao::factory()->create([
            'data_prevista_devolucao' => Carbon::parse('2025-01-10'),
        ]);

        // Act
        $diasAtraso = $locacao->dias_atraso;

        // Assert
        $this->assertEquals(10, $diasAtraso);
    }

    /**
     * Teste: Dias de atraso é zero quando não está atrasado
     */
    public function test_dias_atraso_zero_quando_nao_atrasado(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create([
            'data_prevista_devolucao' => now()->addDays(5),
        ]);

        // Act
        $diasAtraso = $locacao->dias_atraso;

        // Assert
        $this->assertEquals(0, $diasAtraso);
    }
}