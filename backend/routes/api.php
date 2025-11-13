<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FilmeController;
use App\Http\Controllers\Api\LocacaoController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas autenticadas
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/password', [UserController::class, 'updatePassword']);

    // Filmes (todos podem ver)
    Route::get('/filmes', [FilmeController::class, 'index']);
    Route::get('/filmes/disponiveis', [FilmeController::class, 'disponiveis']);
    Route::get('/filmes/categoria/{categoria}', [FilmeController::class, 'porCategoria']);
    Route::get('/filmes/{filme}', [FilmeController::class, 'show']);

    // Filmes (apenas atendente e admin podem gerenciar)
    Route::middleware('role:atendente,administrador')->group(function () {
        Route::post('/filmes', [FilmeController::class, 'store']);
        Route::put('/filmes/{filme}', [FilmeController::class, 'update']);
    });

    // Filmes (apenas admin pode deletar)
    Route::middleware('role:administrador')->group(function () {
        Route::delete('/filmes/{filme}', [FilmeController::class, 'destroy']);
    });

    // Locações
    Route::get('/locacoes', [LocacaoController::class, 'index']);
    Route::get('/locacoes/ativas', [LocacaoController::class, 'ativas']);
    Route::get('/locacoes/{locacao}', [LocacaoController::class, 'show']);
    Route::post('/locacoes', [LocacaoController::class, 'store']);
    Route::post('/locacoes/{locacao}/devolver', [LocacaoController::class, 'devolver']);

    // Locações (apenas admin)
    Route::middleware('role:administrador')->group(function () {
        Route::get('/admin/devolucoes-hoje', [LocacaoController::class, 'devolucoesPrevistaHoje']);
    });

    // Usuários (apenas atendente e admin)
    Route::middleware('role:atendente,administrador')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/clientes', [UserController::class, 'clientes']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});
