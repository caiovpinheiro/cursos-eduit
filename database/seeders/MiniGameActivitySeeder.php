<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MiniGameActivity;
use App\Models\Activity;
use App\Models\ActivityType;

class MiniGameActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $miniGameType = ActivityType::where('name', 'mini_game')->first();
        
        $miniGames = [
            [
                'game_type' => 'memory_cards',
                'config' => [
                    'cards' => [
                        ['id' => 1, 'content' => 'Laravel', 'pair' => 1],
                        ['id' => 2, 'content' => 'Framework PHP', 'pair' => 1],
                        ['id' => 3, 'content' => 'Eloquent', 'pair' => 2],
                        ['id' => 4, 'content' => 'ORM', 'pair' => 2],
                        ['id' => 5, 'content' => 'Blade', 'pair' => 3],
                        ['id' => 6, 'content' => 'Template Engine', 'pair' => 3],
                    ],
                    'difficulty' => 'easy',
                    'theme' => 'laravel'
                ],
                'max_score' => 100,
                'course_id' => 1,
                'title' => 'Jogo da Memória: Conceitos Laravel',
                'order' => 6,
            ],
            [
                'game_type' => 'code_typing',
                'config' => [
                    'code_snippets' => [
                        '<?php echo "Hello World"; ?>',
                        'function test() { return true; }',
                        'class MyClass { public $property; }'
                    ],
                    'time_limit' => 60,
                    'difficulty' => 'medium'
                ],
                'max_score' => 150,
                'course_id' => 2,
                'title' => 'Digitação de Código PHP',
                'order' => 5,
            ],
            [
                'game_type' => 'api_builder',
                'config' => [
                    'endpoints' => [
                        ['method' => 'GET', 'path' => '/users', 'description' => 'Listar usuários'],
                        ['method' => 'POST', 'path' => '/users', 'description' => 'Criar usuário'],
                        ['method' => 'PUT', 'path' => '/users/{id}', 'description' => 'Atualizar usuário'],
                        ['method' => 'DELETE', 'path' => '/users/{id}', 'description' => 'Deletar usuário']
                    ],
                    'scoring' => 'points_per_correct',
                    'time_bonus' => true
                ],
                'max_score' => 200,
                'course_id' => 3,
                'title' => 'Construtor de API REST',
                'order' => 5,
            ],
            [
                'game_type' => 'docker_puzzle',
                'config' => [
                    'puzzle_pieces' => [
                        'FROM php:8.2-fpm',
                        'COPY . /var/www/html',
                        'RUN composer install',
                        'EXPOSE 80',
                        'CMD ["php", "artisan", "serve"]'
                    ],
                    'correct_order' => [0, 1, 2, 3, 4],
                    'hints' => true
                ],
                'max_score' => 120,
                'course_id' => 4,
                'title' => 'Quebra-cabeça Dockerfile',
                'order' => 5,
            ],
            [
                'game_type' => 'test_scenario',
                'config' => [
                    'scenarios' => [
                        [
                            'description' => 'Teste uma função que calcula a soma de dois números',
                            'expected_behavior' => 'Retorna a soma correta',
                            'test_cases' => [
                                ['input' => [2, 3], 'expected' => 5],
                                ['input' => [-1, 1], 'expected' => 0],
                                ['input' => [0, 0], 'expected' => 0]
                            ]
                        ]
                    ],
                    'difficulty' => 'hard',
                    'time_limit' => 300
                ],
                'max_score' => 180,
                'course_id' => 5,
                'title' => 'Cenários de Teste',
                'order' => 4,
            ],
        ];

        foreach ($miniGames as $gameData) {
            $miniGame = MiniGameActivity::create([
                'game_type' => $gameData['game_type'],
                'config' => $gameData['config'],
                'max_score' => $gameData['max_score'],
            ]);

            Activity::create([
                'course_id' => $gameData['course_id'],
                'activity_type_id' => $miniGameType->id,
                'title' => $gameData['title'],
                'order' => $gameData['order'],
                'activityable_id' => $miniGame->id,
                'activityable_type' => MiniGameActivity::class,
            ]);
        }
    }
}