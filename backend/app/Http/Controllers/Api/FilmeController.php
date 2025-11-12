<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Filme;
use App\Services\FilmeService;
use Illuminate\Http\Request;

class FilmeController extends Controller
{
    public function __construct(
        private FilmeService $filmeService
    ) {}


    public function index()
    {
        $filmes = $this->filmeService->getCatalogo();

        return response()->json($filmes);
    }


    public function disponiveis()
    {
        $filmes = $this->filmeService->getDisponiveis();

        return response()->json($filmes);
    }


    public function show(Filme $filme)
    {
        return response()->json($filme);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'sinopse' => 'required|string',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'categoria' => 'required|string|max:50',
            'valor_locacao' => 'required|numeric|min:0',
            'quantidade_disponivel' => 'required|integer|min:0',
        ]);

        $filme = $this->filmeService->criar($validated);

        return response()->json($filme, 201);
    }


    public function update(Request $request, Filme $filme)
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'sinopse' => 'sometimes|string',
            'ano' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'categoria' => 'sometimes|string|max:50',
            'valor_locacao' => 'sometimes|numeric|min:0',
            'quantidade_disponivel' => 'sometimes|integer|min:0',
        ]);

        $filme = $this->filmeService->atualizar($filme, $validated);

        return response()->json($filme);
    }

    /**
     * Deletar filme (apenas admin)
     */
    public function destroy(Filme $filme)
    {
        try {
            $this->filmeService->deletar($filme);

            return response()->json([
                'message' => 'Filme deletado com sucesso.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Buscar por categoria
     */
    public function porCategoria(string $categoria)
    {
        $filmes = $this->filmeService->buscarPorCategoria($categoria);

        return response()->json($filmes);
    }
}
