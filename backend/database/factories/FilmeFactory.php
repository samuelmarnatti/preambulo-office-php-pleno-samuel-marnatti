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
            'imagem_url' => fake()->randomElement([
                'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'https://image.tmdb.org/t/p/w500/w2PMyoyLU22YvrGK3smVM9fW1jj.jpg',
                null // Alguns filmes sem imagem
            ]),
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
