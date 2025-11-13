<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Filme extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'sinopse',
        'ano',
        'categoria',
        'valor_locacao',
        'quantidade_disponivel',
        'imagem_url',
    ];

    protected $casts = [
        'ano' => 'integer',
        'valor_locacao' => 'decimal:2',
        'quantidade_disponivel' => 'integer',
    ];

    // Relacionamentos
    public function locacoes()
    {
        return $this->belongsToMany(Locacao::class, 'locacao_filme')
            ->withTimestamps();
    }

    // Cache-aside pattern
    protected static function booted()
    {
        // Invalidar cache ao salvar ou deletar
        static::saved(function () {
            Cache::forget('filmes.catalogo');
            Cache::forget('filmes.disponiveis');
        });

        static::deleted(function () {
            Cache::forget('filmes.catalogo');
            Cache::forget('filmes.disponiveis');
        });
    }

    // Métodos estáticos para cache
    public static function getCatalogo()
    {
        return Cache::remember('filmes.catalogo', 3600, function () {
            return self::orderBy('titulo')->get();
        });
    }

    public static function getDisponiveis()
    {
        return Cache::remember('filmes.disponiveis', 3600, function () {
            return self::where('quantidade_disponivel', '>', 0)
                ->orderBy('titulo')
                ->get();
        });
    }

    // Scopes
    public function scopeDisponiveis($query)
    {
        return $query->where('quantidade_disponivel', '>', 0);
    }

    public function scopePorCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorAno($query, int $ano)
    {
        return $query->where('ano', $ano);
    }

    // Helpers
    public function estaDisponivel(): bool
    {
        return $this->quantidade_disponivel > 0;
    }

    public function decrementarEstoque(): void
    {
        $this->decrement('quantidade_disponivel');
    }

    public function incrementarEstoque(): void
    {
        $this->increment('quantidade_disponivel');
    }
}
