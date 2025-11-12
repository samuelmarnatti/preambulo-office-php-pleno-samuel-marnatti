<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $role = $request->query('role');

        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name')->get();

        return response()->json($users);
    }


    public function clientes()
    {
        $clientes = User::clientes()->orderBy('name')->get();

        return response()->json($clientes);
    }


    public function show(User $user)
    {
        return response()->json($user);
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'cpf' => 'sometimes|string|size:11|unique:users,cpf,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'role' => 'sometimes|in:cliente,atendente,administrador',
        ]);

        $user->update($validated);

        return response()->json($user->fresh());
    }

    /**
     * Deletar usuário (apenas admin)
     */
    public function destroy(User $user)
    {
        // Não permitir deletar se tiver locações ativas
        if ($user->temDevolucoesPendentes()) {
            return response()->json([
                'message' => 'Não é possível deletar usuário com devoluções pendentes.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuário deletado com sucesso.',
        ]);
    }

    /**
     * Atualizar senha
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Senha atual incorreta.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Senha atualizada com sucesso.',
        ]);
    }
}
