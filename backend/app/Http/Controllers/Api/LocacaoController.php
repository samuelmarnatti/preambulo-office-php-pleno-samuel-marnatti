<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Locacao;
use App\Services\LocacaoService;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    public function __construct(
        private LocacaoService $locacaoService
    ) {}


    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isCliente()) {
            // Cliente vê apenas suas locações
            $locacoes = Locacao::with('filmes')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Atendente e Admin veem todas
            $locacoes = Locacao::with(['cliente', 'filmes'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($locacoes);
    }

    /**
     * Exibir locação específica
     */
    public function show(Request $request, Locacao $locacao)
    {
        $user = $request->user();

        // Cliente só pode ver suas próprias locações
        if ($user->isCliente() && $locacao->user_id !== $user->id) {
            return response()->json([
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        return response()->json($locacao->load(['cliente', 'filmes']));
    }

    /**
     * Criar nova locação
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'filme_ids' => 'required|array|min:1',
            'filme_ids.*' => 'required|exists:filmes,id',
            'dias_locacao' => 'sometimes|integer|min:1|max:30',
        ]);

        try {
            $locacao = $this->locacaoService->criar(
                $request->user(),
                $validated['filme_ids'],
                $validated['dias_locacao'] ?? 7
            );

            return response()->json($locacao, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Devolver locação
     */
    public function devolver(Request $request, Locacao $locacao)
    {
        $user = $request->user();

        // Cliente só pode devolver suas próprias locações
        if ($user->isCliente() && $locacao->user_id !== $user->id) {
            return response()->json([
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        try {
            $locacao = $this->locacaoService->devolver($locacao);

            return response()->json($locacao);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Locações ativas do cliente
     */
    public function ativas(Request $request)
    {
        $locacoes = Locacao::with('filmes')
            ->where('user_id', $request->user()->id)
            ->pendentes()
            ->get();

        return response()->json($locacoes);
    }

    /**
     * Devoluções previstas para hoje (apenas admin)
     */
    public function devolucoesPrevistaHoje()
    {
        $locacoes = $this->locacaoService->getDevolucoesPrevistaHoje();

        return response()->json($locacoes);
    }
}
