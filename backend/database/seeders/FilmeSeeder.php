<?php

namespace Database\Seeders;

use App\Models\Filme;
use Illuminate\Database\Seeder;

class FilmeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filmes = [
            [
                'titulo' => 'Oppenheimer',
                'sinopse' => 'A história do físico J. Robert Oppenheimer e seu papel no desenvolvimento da bomba atômica durante a Segunda Guerra Mundial.',
                'ano' => 2023,
                'categoria' => 'Drama',
                'valor_locacao' => 15.90,
                'quantidade_disponivel' => 5,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
            ],
            [
                'titulo' => 'Barbie',
                'sinopse' => 'Depois de ser expulsa da Barbieland por ser uma boneca de aparência menos que perfeita, Barbie parte para o mundo humano em busca da verdadeira felicidade.',
                'ano' => 2023,
                'categoria' => 'Comédia',
                'valor_locacao' => 14.90,
                'quantidade_disponivel' => 8,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/iuFNMS8U5cb6xfzi51Dbkovj7vM.jpg',
            ],
            [
                'titulo' => 'Avatar: O Caminho da Água',
                'sinopse' => 'Jake Sully e Neytiri formaram uma família e estão fazendo de tudo para ficarem juntos. No entanto, eles devem sair de casa e explorar as regiões de Pandora.',
                'ano' => 2022,
                'categoria' => 'Ficção Científica',
                'valor_locacao' => 16.90,
                'quantidade_disponivel' => 6,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg',
            ],
            [
                'titulo' => 'Top Gun: Maverick',
                'sinopse' => 'Após mais de 30 anos de serviço, Pete "Maverick" Mitchell continua a testar os limites como piloto de caça da Marinha, evitando a promoção que o colocaria em terra.',
                'ano' => 2022,
                'categoria' => 'Ação',
                'valor_locacao' => 15.90,
                'quantidade_disponivel' => 7,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/62HCnUTziyWcpDaBO2i1DX17ljH.jpg',
            ],
            [
                'titulo' => 'Guardiões da Galáxia Vol. 3',
                'sinopse' => 'Peter Quill deve reunir sua equipe para defender o universo e proteger um dos seus. Se a missão não for totalmente bem-sucedida, isso pode levar ao fim dos Guardiões.',
                'ano' => 2023,
                'categoria' => 'Ação',
                'valor_locacao' => 15.90,
                'quantidade_disponivel' => 4,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/r2J02Z2OpNTctfOSN1Ydgii51I3.jpg',
            ],
            [
                'titulo' => 'Homem-Aranha: Através do Aranhaverso',
                'sinopse' => 'Miles Morales retorna para uma nova aventura através do multiverso com Gwen Stacy e uma nova equipe de Pessoas-Aranha.',
                'ano' => 2023,
                'categoria' => 'Animação',
                'valor_locacao' => 14.90,
                'quantidade_disponivel' => 10,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg',
            ],
            [
                'titulo' => 'John Wick 4: Baba Yaga',
                'sinopse' => 'John Wick descobre um caminho para derrotar a Alta Cúpula. Mas antes que ele possa conquistar sua liberdade, precisa enfrentar um novo inimigo.',
                'ano' => 2023,
                'categoria' => 'Ação',
                'valor_locacao' => 15.90,
                'quantidade_disponivel' => 5,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/vZloFAK7NmvMGKE7VkF5UHaz0I.jpg',
            ],
            [
                'titulo' => 'Dungeons & Dragons: Honra Entre Rebeldes',
                'sinopse' => 'Um ladrão encantador e um grupo de aventureiros improváveis embarcam em uma jornada épica para recuperar uma relíquia perdida.',
                'ano' => 2023,
                'categoria' => 'Aventura',
                'valor_locacao' => 14.90,
                'quantidade_disponivel' => 6,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/A7AoNT06aRAc4SV89Dwxj3EYAgC.jpg',
            ],
            [
                'titulo' => 'Elementos',
                'sinopse' => 'Em uma cidade onde os residentes do fogo, água, terra e ar vivem juntos, uma jovem impetuosa e um cara que se deixa levar descobrem algo elementar: o quanto têm em comum.',
                'ano' => 2023,
                'categoria' => 'Animação',
                'valor_locacao' => 13.90,
                'quantidade_disponivel' => 8,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/4Y1WNkd88JXmGfhtWR7dmDAo1T2.jpg',
            ],
            [
                'titulo' => 'Invocação do Mal 3: A Ordem do Demônio',
                'sinopse' => 'Ed e Lorraine Warren investigam um dos casos mais sensacionais de seus arquivos: a luta pela alma de um garoto e o primeiro julgamento por assassinato em que a possessão demoníaca é usada como defesa.',
                'ano' => 2021,
                'categoria' => 'Terror',
                'valor_locacao' => 12.90,
                'quantidade_disponivel' => 4,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/xbEhcK7679VMj8BbGCJKNjLQsIy.jpg',
            ],
            [
                'titulo' => 'Missão: Impossível - Acerto de Contas Parte 1',
                'sinopse' => 'Ethan Hunt e sua equipe da IMF embarcam em sua missão mais perigosa de todos os tempos: rastrear uma nova arma antes que caia em mãos erradas.',
                'ano' => 2023,
                'categoria' => 'Ação',
                'valor_locacao' => 16.90,
                'quantidade_disponivel' => 5,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/NNxYkU70HPurnNCSiCjYAmacwm.jpg',
            ],
            [
                'titulo' => 'A Pequena Sereia',
                'sinopse' => 'A jovem sereia Ariel faz um acordo com a bruxa do mar Úrsula para transformar-se em humana e conquistar um príncipe.',
                'ano' => 2023,
                'categoria' => 'Aventura',
                'valor_locacao' => 14.90,
                'quantidade_disponivel' => 7,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/ym1dxyOk4jFcSl4Q2zmRrA5BEEN.jpg',
            ],
            [
                'titulo' => 'Velozes & Furiosos 10',
                'sinopse' => 'Ao longo de muitas missões e contra todas as probabilidades, Dom Toretto e sua família foram mais espertos e mais rápidos do que todos os inimigos em seu caminho.',
                'ano' => 2023,
                'categoria' => 'Ação',
                'valor_locacao' => 15.90,
                'quantidade_disponivel' => 6,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/fiVW06jE7z9YnO4trhaMEdclSiC.jpg',
            ],
            [
                'titulo' => 'M3GAN',
                'sinopse' => 'Uma brilhante roboticista cria uma boneca com inteligência artificial projetada para ser a melhor companheira de uma criança, mas as coisas saem terrivelmente erradas.',
                'ano' => 2023,
                'categoria' => 'Terror',
                'valor_locacao' => 13.90,
                'quantidade_disponivel' => 5,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/xYLBgw7dHyEqmcrSk2Sq3asuSq5.jpg',
            ],
            [
                'titulo' => 'A Freira 2',
                'sinopse' => 'Em 1956, na França, um padre é assassinado e parece que um mal está se espalhando. A Irmã Irene mais uma vez fica cara a cara com a força demoníaca.',
                'ano' => 2023,
                'categoria' => 'Terror',
                'valor_locacao' => 14.90,
                'quantidade_disponivel' => 8,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/5gzzkR7y3hnY8AD1wXjCnVlHba5.jpg',
            ],
            [
                'titulo' => 'O Exorcista do Papa',
                'sinopse' => 'O Padre Gabriele Amorth, exorcista-chefe do Vaticano, investiga a possessão de uma criança e acaba descobrindo uma conspiração secular.',
                'ano' => 2023,
                'categoria' => 'Terror',
                'valor_locacao' => 13.90,
                'quantidade_disponivel' => 4,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/9JBEPLTPSm0d1mbEcLxULjJq9Eh.jpg',
            ],
            [
                'titulo' => 'Indiana Jones e a Relíquia do Destino',
                'sinopse' => 'O lendário herói arqueólogo está de volta. Harrison Ford retorna ao papel icônico para esta aventura final.',
                'ano' => 2023,
                'categoria' => 'Aventura',
                'valor_locacao' => 16.90,
                'quantidade_disponivel' => 3,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/qDSKxeHoN2SEjsuBTEm8YWgr5vV.jpg',
            ],
            [
                'titulo' => 'Transformers: O Despertar das Feras',
                'sinopse' => 'Durante os anos 90, uma nova facção de Transformers chega à Terra, os Maximals, e se junta aos Autobots como aliados na batalha pela Terra.',
                'ano' => 2023,
                'categoria' => 'Ação',
                'valor_locacao' => 15.90,
                'quantidade_disponivel' => 5,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/gPbM0MK8CP8A174rmUwGsADNYKD.jpg',
            ],
            [
                'titulo' => 'Scream VI',
                'sinopse' => 'Os quatro sobreviventes dos assassinatos Ghostface deixam Woodsboro para trás e iniciam um novo capítulo em Nova York.',
                'ano' => 2023,
                'categoria' => 'Terror',
                'valor_locacao' => 14.90,
                'quantidade_disponivel' => 6,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/wDWwtvkRRlgTiUr6TyLSMX8FCuZ.jpg',
            ],
            [
                'titulo' => 'Renfield: Dando Sangue pelo Chefe',
                'sinopse' => 'Renfield, o sofrido ajudante de Drácula, é forçado a fazer as licitações de seu mestre. Mas agora, após séculos de servidão, ele está pronto para descobrir se há vida fora da sombra do Príncipe das Trevas.',
                'ano' => 2023,
                'categoria' => 'Comédia',
                'valor_locacao' => 13.90,
                'quantidade_disponivel' => 7,
                'imagem_url' => 'https://image.tmdb.org/t/p/w500/p6y5JOdhSJZhumVjNJ7VcTRBnTL.jpg',
            ],
        ];

        foreach ($filmes as $filme) {
            Filme::create($filme);
        }
    }
}
