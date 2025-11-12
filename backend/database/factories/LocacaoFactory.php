<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocacaoFactory extends Factory
{
    public function definition(): array
    {
        $dataInicio = now()->subDays(fake()->numberBetween(1, 10));
        $dataPrevista = $dataInicio->copy()->addDays(7);
        
        return [
            'user_id' => User::factory(),
            'data_inicio' => $dataInicio,
            'data_prevista_devolucao' => $dataPrevista,
            'data_devolucao' => null,
            'status' => 'ativo',
            'valor_total' => fake()->randomFloat(2, 10, 100),
            'multa' => 0,
        ];
    }

    public function ativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ativo',
            'data_devolucao' => null,
        ]);
    }

    public function atrasada(): static
    {
        $dataInicio = now()->subDays(15);
        $dataPrevista = $dataInicio->copy()->addDays(7);
        
        return $this->state(fn (array $attributes) => [
            'status' => 'atrasado',
            'data_inicio' => $dataInicio,
            'data_prevista_devolucao' => $dataPrevista,
            'data_devolucao' => null,
            'multa' => 40.00, // 8 dias * 5.00 * 1 filme
        ]);
    }

    public function devolvida(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'devolvido',
            'data_devolucao' => now(),
        ]);
    }
}