<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Locacao extends Model
{
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'user_id',
        'data_inicio',
        'data_prevista_devolucao',
        'data_devolucao',
        'status',
        'valor_total',
        'multa',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_prevista_devolucao' => 'date',
        'data_devolucao' => 'date',
        'valor_total' => 'decimal:2',
        'multa' => 'decimal:2',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function filmes()
    {
        return $this->belongsToMany(Filme::class, 'locacao_filme')
            ->withTimestamps();
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeAtrasadas($query)
    {
        return $query->where('status', 'atrasado');
    }

    public function scopeDevolvidas($query)
    {
        return $query->where('status', 'devolvido');
    }

    public function scopeComDevolucaoHoje($query)
    {
        return $query->where('data_prevista_devolucao', now()->toDateString())
            ->whereIn('status', ['ativo', 'atrasado']);
    }

    public function scopePendentes($query)
    {
        return $query->whereIn('status', ['ativo', 'atrasado']);
    }

    // Helpers de status
    public function estaAtiva(): bool
    {
        return $this->status === 'ativo';
    }

    public function estaAtrasada(): bool
    {
        return $this->status === 'atrasado' || 
               ($this->estaAtiva() && now()->isAfter($this->data_prevista_devolucao));
    }

    public function foiDevolvida(): bool
    {
        return $this->status === 'devolvido';
    }

    // Cálculo de multa
    public function calcularMulta(): float
    {
        if (now()->lte($this->data_prevista_devolucao)) {
            return 0;
        }

        $diasAtraso = now()->diffInDays($this->data_prevista_devolucao);
        $quantidadeFilmes = $this->filmes()->count();
        
        // R$ 5,00 por dia por filme
        return $diasAtraso * 5.00 * $quantidadeFilmes;
    }

    public function getDiasAtrasoAttribute(): int
    {
        if (now()->lte($this->data_prevista_devolucao)) {
            return 0;
        }

        return now()->diffInDays($this->data_prevista_devolucao);
    }

    public function atualizarStatus(): void
    {
        if ($this->foiDevolvida()) {
            return;
        }

        if ($this->estaAtrasada()) {
            $this->update([
                'status' => 'atrasado',
                'multa' => $this->calcularMulta(),
            ]);
        }
    }

    // Eventos do modelo
    protected static function booted()
    {
        // Atualizar status automaticamente ao carregar
        static::retrieved(function ($locacao) {
            $locacao->atualizarStatus();
        });
    }
}