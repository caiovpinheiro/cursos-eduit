<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\VideoActivity;
use App\Models\ArticleActivity;
use App\Models\QuizActivity;
use App\Models\MiniGameActivity;

class UpdateCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Deletar cursos antigos (IDs 1-9) e suas atividades
        echo "Deletando cursos antigos...\n";
        $oldCourses = Course::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9])->get();
        
        foreach ($oldCourses as $course) {
            // Deletar atividades primeiro
            $activities = $course->activities;
            foreach ($activities as $activity) {
                if ($activity->activityable) {
                    $activity->activityable->delete();
                }
                $activity->delete();
            }
            $course->delete();
        }

        // 2. Atualizar curso descontinuado para ter imagem de capa
        echo "Atualizando curso descontinuado...\n";
        $discontinuedCourse = Course::find(18);
        if ($discontinuedCourse) {
            $discontinuedCourse->update([
                'cover_image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
            ]);
        }

        // 3. Criar atividades para os cursos novos
        echo "Criando atividades para cursos novos...\n";
        
        // Obter tipos de atividade
        $videoType = ActivityType::where('name', 'video')->first();
        $articleType = ActivityType::where('name', 'article')->first();
        $quizType = ActivityType::where('name', 'quiz')->first();
        $miniGameType = ActivityType::where('name', 'mini_game')->first();

        // Atividades para Laravel Avançado (ID 10)
        $this->createCourseActivities(10, [
            ['title' => 'Introdução ao Laravel', 'type' => 'video', 'duration' => 30],
            ['title' => 'Configuração do Ambiente', 'type' => 'video', 'duration' => 45],
            ['title' => 'Rotas e Controllers', 'type' => 'video', 'duration' => 60],
            ['title' => 'Eloquent ORM', 'type' => 'video', 'duration' => 90],
            ['title' => 'Autenticação e Autorização', 'type' => 'video', 'duration' => 75],
            ['title' => 'APIs RESTful', 'type' => 'video', 'duration' => 120],
            ['title' => 'Testes Automatizados', 'type' => 'video', 'duration' => 90],
            ['title' => 'Deploy em Produção', 'type' => 'video', 'duration' => 60],
            ['title' => 'Fundamentos do Laravel', 'type' => 'article', 'duration' => 15],
            ['title' => 'Boas Práticas de Desenvolvimento', 'type' => 'article', 'duration' => 20],
            ['title' => 'Quiz: Conceitos Laravel', 'type' => 'quiz', 'duration' => 10],
            ['title' => 'Desafio: Criando uma API', 'type' => 'mini_game', 'duration' => 30],
        ]);

        // Atividades para PHP Moderno (ID 11)
        $this->createCourseActivities(11, [
            ['title' => 'PHP 8+ Novidades', 'type' => 'video', 'duration' => 45],
            ['title' => 'Programação Orientada a Objetos', 'type' => 'video', 'duration' => 90],
            ['title' => 'Padrões de Design', 'type' => 'video', 'duration' => 120],
            ['title' => 'PSR Standards', 'type' => 'video', 'duration' => 60],
            ['title' => 'Performance e Otimização', 'type' => 'video', 'duration' => 75],
            ['title' => 'PHP 8+ Recursos Avançados', 'type' => 'article', 'duration' => 20],
            ['title' => 'Quiz: PHP 8+', 'type' => 'quiz', 'duration' => 15],
            ['title' => 'Desafio: Código Limpo', 'type' => 'mini_game', 'duration' => 25],
        ]);

        // Atividades para APIs RESTful (ID 12)
        $this->createCourseActivities(12, [
            ['title' => 'Fundamentos de APIs REST', 'type' => 'video', 'duration' => 60],
            ['title' => 'Laravel API Resources', 'type' => 'video', 'duration' => 90],
            ['title' => 'Autenticação JWT', 'type' => 'video', 'duration' => 75],
            ['title' => 'Validação e Tratamento de Erros', 'type' => 'video', 'duration' => 60],
            ['title' => 'Documentação com Swagger', 'type' => 'video', 'duration' => 45],
            ['title' => 'Testes de API', 'type' => 'video', 'duration' => 90],
            ['title' => 'Conceitos de API REST', 'type' => 'article', 'duration' => 15],
            ['title' => 'Quiz: APIs RESTful', 'type' => 'quiz', 'duration' => 20],
            ['title' => 'Desafio: Construindo uma API', 'type' => 'mini_game', 'duration' => 45],
            ['title' => 'Projeto Final: API Completa', 'type' => 'video', 'duration' => 120],
        ]);

        // Atividades para Docker (ID 13)
        $this->createCourseActivities(13, [
            ['title' => 'Conceitos de Containerização', 'type' => 'video', 'duration' => 45],
            ['title' => 'Dockerfile e Docker Compose', 'type' => 'video', 'duration' => 90],
            ['title' => 'Volumes e Networks', 'type' => 'video', 'duration' => 60],
            ['title' => 'Orquestração com Docker Swarm', 'type' => 'video', 'duration' => 75],
            ['title' => 'Deploy em Produção', 'type' => 'video', 'duration' => 60],
            ['title' => 'Docker Básico', 'type' => 'article', 'duration' => 20],
            ['title' => 'Quiz: Docker', 'type' => 'quiz', 'duration' => 15],
            ['title' => 'Desafio: Containerizando Aplicação', 'type' => 'mini_game', 'duration' => 30],
        ]);

        // Atividades para PHPUnit (ID 14)
        $this->createCourseActivities(14, [
            ['title' => 'Fundamentos de Testes', 'type' => 'video', 'duration' => 45],
            ['title' => 'PHPUnit Avançado', 'type' => 'video', 'duration' => 90],
            ['title' => 'Mocks e Stubs', 'type' => 'video', 'duration' => 75],
            ['title' => 'Testes de Integração', 'type' => 'video', 'duration' => 60],
            ['title' => 'CI/CD com Testes', 'type' => 'video', 'duration' => 45],
            ['title' => 'Fundamentos de Testes', 'type' => 'article', 'duration' => 20],
            ['title' => 'Quiz: Testes Automatizados', 'type' => 'quiz', 'duration' => 15],
            ['title' => 'Desafio: Cenários de Teste', 'type' => 'mini_game', 'duration' => 25],
        ]);

        // Atividades para MySQL (ID 15)
        $this->createCourseActivities(15, [
            ['title' => 'Fundamentos de SQL', 'type' => 'video', 'duration' => 60],
            ['title' => 'Design de Banco de Dados', 'type' => 'video', 'duration' => 90],
            ['title' => 'Índices e Performance', 'type' => 'video', 'duration' => 75],
            ['title' => 'Transações e ACID', 'type' => 'video', 'duration' => 60],
            ['title' => 'Backup e Recuperação', 'type' => 'video', 'duration' => 45],
            ['title' => 'Fundamentos de SQL', 'type' => 'article', 'duration' => 25],
            ['title' => 'Quiz: MySQL', 'type' => 'quiz', 'duration' => 20],
            ['title' => 'Desafio: Otimização de Queries', 'type' => 'mini_game', 'duration' => 35],
            ['title' => 'Projeto: Sistema de Vendas', 'type' => 'video', 'duration' => 120],
        ]);

        // Atividades para Git (ID 16)
        $this->createCourseActivities(16, [
            ['title' => 'Fundamentos do Git', 'type' => 'video', 'duration' => 30],
            ['title' => 'Branching e Merging', 'type' => 'video', 'duration' => 45],
            ['title' => 'GitHub e GitLab', 'type' => 'video', 'duration' => 60],
            ['title' => 'Workflows em Equipe', 'type' => 'video', 'duration' => 45],
            ['title' => 'Resolução de Conflitos', 'type' => 'video', 'duration' => 30],
        ]);

        // Atividades para Liderança (ID 17)
        $this->createCourseActivities(17, [
            ['title' => 'Fundamentos de Liderança', 'type' => 'video', 'duration' => 60],
            ['title' => 'Gestão de Pessoas', 'type' => 'video', 'duration' => 90],
            ['title' => 'Comunicação Eficaz', 'type' => 'video', 'duration' => 75],
            ['title' => 'Metodologias Ágeis', 'type' => 'video', 'duration' => 60],
            ['title' => 'Resolução de Conflitos', 'type' => 'video', 'duration' => 45],
            ['title' => 'Liderança Situacional', 'type' => 'article', 'duration' => 20],
        ]);

        echo "Atualização concluída!\n";
    }

    private function createCourseActivities($courseId, $activities)
    {
        $videoType = ActivityType::where('name', 'video')->first();
        $articleType = ActivityType::where('name', 'article')->first();
        $quizType = ActivityType::where('name', 'quiz')->first();
        $miniGameType = ActivityType::where('name', 'mini_game')->first();

        foreach ($activities as $index => $activityData) {
            $activityable = null;
            $activityType = null;

            switch ($activityData['type']) {
                case 'video':
                    $activityable = VideoActivity::create([
                        'url' => 'https://example.com/video/' . ($index + 1),
                        'duration' => $activityData['duration'],
                    ]);
                    $activityType = $videoType;
                    break;
                case 'article':
                    $activityable = ArticleActivity::create([
                        'content' => 'Conteúdo do artigo: ' . $activityData['title'],
                    ]);
                    $activityType = $articleType;
                    break;
                case 'quiz':
                    $activityable = QuizActivity::create([
                        'questions' => [
                            ['question' => 'Pergunta 1', 'options' => ['A', 'B', 'C', 'D'], 'correct' => 0],
                            ['question' => 'Pergunta 2', 'options' => ['A', 'B', 'C', 'D'], 'correct' => 1],
                        ],
                        'time_limit' => $activityData['duration'],
                        'passing_score' => 70,
                    ]);
                    $activityType = $quizType;
                    break;
                case 'mini_game':
                    $activityable = MiniGameActivity::create([
                        'game_type' => 'challenge',
                        'config' => ['level' => $index + 1],
                        'max_score' => 100,
                    ]);
                    $activityType = $miniGameType;
                    break;
            }

            if ($activityable && $activityType) {
                Activity::create([
                    'course_id' => $courseId,
                    'activity_type_id' => $activityType->id,
                    'title' => $activityData['title'],
                    'order' => $index + 1,
                    'activityable_id' => $activityable->id,
                    'activityable_type' => get_class($activityable),
                ]);
            }
        }
    }
}
