<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'cpf',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relacionamentos
    public function locacoes()
    {
        return $this->hasMany(Locacao::class);
    }

    // Scopes
    public function scopeClientes($query)
    {
        return $query->where('role', 'cliente');
    }

    public function scopeAtendentes($query)
    {
        return $query->where('role', 'atendente');
    }

    public function scopeAdministradores($query)
    {
        return $query->where('role', 'administrador');
    }

    // Helpers
    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    public function isAtendente(): bool
    {
        return $this->role === 'atendente';
    }

    public function isAdministrador(): bool
    {
        return $this->role === 'administrador';
    }

    public function temDevolucoesPendentes(): bool
    {
        return $this->locacoes()
            ->whereIn('status', ['ativo', 'atrasado'])
            ->exists();
    }

    public function podeAlugar(): bool
    {
        return !$this->temDevolucoesPendentes();
    }
}