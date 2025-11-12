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
    
