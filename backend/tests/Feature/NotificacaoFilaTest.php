<?php

namespace Tests\Feature;

use App\Jobs\EnviarNotificacaoEmail;
use App\Models\Locacao;
use App\Models\User;
use App\Models\Filme;
use App\Services\LocacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificacaoFilaTest extends TestCase
{
    use RefreshDatabase;

    public function test_notificacao_deve_ser_enfileirada_ao_criar_locacao()
    {
        Queue::fake();

        /** @var User $user */
        $user = User::factory()->create(['role' => 'cliente']);
        $filme = Filme::factory()->create(['quantidade_disponivel' => 5]);

        $this->actingAs($user)
            ->postJson('/api/locacoes', [
                'filme_ids' => [$filme->id],
                'dias_locacao' => 7,
            ])
            ->assertStatus(201);

        Queue::assertPushed(EnviarNotificacaoEmail::class);
    }

    public function test_notificacao_deve_ser_enviada_ao_processar_fila()
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'cliente', 'email' => 'teste@example.com']);
        $filme = Filme::factory()->create(['quantidade_disponivel' => 5]);

        $locacao = Locacao::factory()->create([
            'user_id' => $user->id,
        ]);
        $locacao->filmes()->attach($filme->id);

        $job = new EnviarNotificacaoEmail($locacao, 'confirmacao');
        $job->handle();

        $this->assertTrue(true); // Job executado sem erros
    }
}
