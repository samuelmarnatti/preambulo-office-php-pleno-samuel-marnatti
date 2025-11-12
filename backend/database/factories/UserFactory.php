<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'cliente',
            'cpf' => fake()->numerify('###########'),
            'phone' => fake()->phoneNumber(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function cliente(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'cliente',
        ]);
    }

    public function atendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'atendente',
            'cpf' => null,
        ]);
    }

    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'administrador',
            'cpf' => null,
        ]);
    }
}