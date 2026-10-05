<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizActivity;
use App\Models\Activity;
use App\Models\ActivityType;

class QuizActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizType = ActivityType::where('name', 'quiz')->first();
        
        $quizzes = [
            [
                'questions' => [
                    [
                        'question' => 'Qual é o comando para criar um novo projeto Laravel?',
                        'options' => [
                            'laravel new projeto',
                            'composer create-project laravel/laravel projeto',
                            'php artisan new projeto',
                            'git clone laravel projeto'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ],
                    [
                        'question' => 'Qual ORM o Laravel utiliza?',
                        'options' => [
                            'Doctrine',
                            'Eloquent',
                            'Propel',
                            'RedBean'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ],
                    [
                        'question' => 'Qual é o nome do template engine do Laravel?',
                        'options' => [
                            'Twig',
                            'Smarty',
                            'Blade',
                            'Mustache'
                        ],
                        'correct_answer' => 2,
                        'points' => 10
                    ]
                ],
                'time_limit' => 15,
                'passing_score' => 70,
                'course_id' => 1,
                'title' => 'Quiz: Fundamentos do Laravel',
                'order' => 5,
            ],
            [
                'questions' => [
                    [
                        'question' => 'Qual recurso foi introduzido no PHP 8?',
                        'options' => [
                            'Union Types',
                            'Namespaces',
                            'Classes',
                            'Interfaces'
                        ],
                        'correct_answer' => 0,
                        'points' => 10
                    ],
                    [
                        'question' => 'O que são Named Arguments?',
                        'options' => [
                            'Argumentos com nomes específicos',
                            'Argumentos posicionais',
                            'Argumentos opcionais',
                            'Argumentos de retorno'
                        ],
                        'correct_answer' => 0,
                        'points' => 10
                    ]
                ],
                'time_limit' => 10,
                'passing_score' => 60,
                'course_id' => 2,
                'title' => 'Quiz: PHP 8+',
                'order' => 4,
            ],
            [
                'questions' => [
                    [
                        'question' => 'Qual método HTTP é usado para criar recursos?',
                        'options' => [
                            'GET',
                            'POST',
                            'PUT',
                            'DELETE'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ],
                    [
                        'question' => 'O que significa REST?',
                        'options' => [
                            'Representational State Transfer',
                            'Remote State Transfer',
                            'Resource State Transfer',
                            'Representational Server Transfer'
                        ],
                        'correct_answer' => 0,
                        'points' => 10
                    ],
                    [
                        'question' => 'Qual código de status indica sucesso na criação?',
                        'options' => [
                            '200',
                            '201',
                            '204',
                            '202'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ]
                ],
                'time_limit' => 12,
                'passing_score' => 80,
                'course_id' => 3,
                'title' => 'Quiz: APIs RESTful',
                'order' => 4,
            ],
            [
                'questions' => [
                    [
                        'question' => 'O que é um container Docker?',
                        'options' => [
                            'Uma máquina virtual',
                            'Um processo isolado com suas dependências',
                            'Um servidor físico',
                            'Um banco de dados'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ],
                    [
                        'question' => 'Qual comando constrói uma imagem Docker?',
                        'options' => [
                            'docker run',
                            'docker build',
                            'docker create',
                            'docker start'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ]
                ],
                'time_limit' => 8,
                'passing_score' => 70,
                'course_id' => 4,
                'title' => 'Quiz: Docker Básico',
                'order' => 4,
            ],
            [
                'questions' => [
                    [
                        'question' => 'Qual é o framework de testes padrão para PHP?',
                        'options' => [
                            'Codeception',
                            'PHPUnit',
                            'Behat',
                            'Selenium'
                        ],
                        'correct_answer' => 1,
                        'points' => 10
                    ],
                    [
                        'question' => 'O que testa um teste unitário?',
                        'options' => [
                            'Integração entre sistemas',
                            'Interface do usuário',
                            'Unidade isolada de código',
                            'Performance da aplicação'
                        ],
                        'correct_answer' => 2,
                        'points' => 10
                    ]
                ],
                'time_limit' => 10,
                'passing_score' => 70,
                'course_id' => 5,
                'title' => 'Quiz: Testes Automatizados',
                'order' => 3,
            ],
        ];

        foreach ($quizzes as $quizData) {
            $quiz = QuizActivity::create([
                'questions' => $quizData['questions'],
                'time_limit' => $quizData['time_limit'],
                'passing_score' => $quizData['passing_score'],
            ]);

            Activity::create([
                'course_id' => $quizData['course_id'],
                'activity_type_id' => $quizType->id,
                'title' => $quizData['title'],
                'order' => $quizData['order'],
                'activityable_id' => $quiz->id,
                'activityable_type' => QuizActivity::class,
            ]);
        }
    }
}