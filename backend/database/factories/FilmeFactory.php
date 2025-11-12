<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FilmeFactory extends Factory
{
    public function definition(): array
    {
        $categorias = ['Ação', 'Comédia', 'Drama', 'Terror', 'Ficção Científica', 'Romance', 'Aventura', 'Suspense'];
        
        return [
            'titulo' => fake()->sentence(3),
            'sinopse' => fake()->paragraph(3),
            'ano' => fake()->numberBetween(1990, 2024),
            'categoria' => fake()->randomElement($categorias),
            'valor_locacao' => fake()->randomFloat(2, 5, 25),
            'quantidade_disponivel' => fake()->numberBetween(0, 10),
        ];
    }

    public function disponivel()
    {
        return $this->state(fn (array $attributes) => [
            'quantidade_disponivel' => fake()->numberBetween(1, 10),
        ]);
    }

    public function indisponivel()
    {
        return $this->state(fn (array $attributes) => [
            'quantidade_disponivel' => 0,
        ]);
    }
}