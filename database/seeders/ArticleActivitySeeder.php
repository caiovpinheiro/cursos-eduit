<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ArticleActivity;
use App\Models\Activity;
use App\Models\ActivityType;

class ArticleActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articleType = ActivityType::where('name', 'article')->first();
        
        $articles = [
            [
                'content' => '<h1>Introdução ao Laravel</h1>
                <p>Laravel é um framework PHP elegante e expressivo que facilita o desenvolvimento de aplicações web modernas.</p>
                <h2>Principais Características</h2>
                <ul>
                    <li>Eloquent ORM para interação com banco de dados</li>
                    <li>Sistema de rotas intuitivo</li>
                    <li>Blade template engine</li>
                    <li>Artisan CLI para automação de tarefas</li>
                </ul>
                <h2>Instalação</h2>
                <p>Para instalar o Laravel, você pode usar o Composer:</p>
                <pre><code>composer create-project laravel/laravel meu-projeto</code></pre>',
                'course_id' => 1,
                'title' => 'Fundamentos do Laravel',
                'order' => 4,
            ],
            [
                'content' => '<h1>PHP 8+ e Suas Novidades</h1>
                <p>PHP 8 introduziu várias melhorias significativas na linguagem.</p>
                <h2>Principais Novidades</h2>
                <ul>
                    <li>Union Types</li>
                    <li>Named Arguments</li>
                    <li>Match Expression</li>
                    <li>Constructor Property Promotion</li>
                </ul>
                <h2>Exemplo de Union Types</h2>
                <pre><code>function processId(string|int $id): void {
    // Processa ID que pode ser string ou int
}</code></pre>',
                'course_id' => 2,
                'title' => 'PHP 8+ Recursos Avançados',
                'order' => 3,
            ],
            [
                'content' => '<h1>Desenvolvendo APIs RESTful</h1>
                <p>APIs RESTful seguem princípios específicos para criar interfaces web escaláveis.</p>
                <h2>Princípios REST</h2>
                <ul>
                    <li>Stateless - cada requisição é independente</li>
                    <li>Client-Server - separação de responsabilidades</li>
                    <li>Cacheable - respostas podem ser cacheadas</li>
                    <li>Uniform Interface - interface consistente</li>
                </ul>
                <h2>Métodos HTTP</h2>
                <ul>
                    <li>GET - recuperar dados</li>
                    <li>POST - criar recursos</li>
                    <li>PUT - atualizar recursos</li>
                    <li>DELETE - remover recursos</li>
                </ul>',
                'course_id' => 3,
                'title' => 'Conceitos de API REST',
                'order' => 3,
            ],
            [
                'content' => '<h1>Docker: Containerização de Aplicações</h1>
                <p>Docker permite empacotar aplicações e suas dependências em containers.</p>
                <h2>Vantagens do Docker</h2>
                <ul>
                    <li>Consistência entre ambientes</li>
                    <li>Isolamento de aplicações</li>
                    <li>Facilidade de deploy</li>
                    <li>Escalabilidade</li>
                </ul>
                <h2>Comandos Básicos</h2>
                <pre><code># Construir imagem
docker build -t minha-app .

# Executar container
docker run -p 8080:80 minha-app

# Listar containers
docker ps</code></pre>',
                'course_id' => 4,
                'title' => 'Docker Básico',
                'order' => 3,
            ],
            [
                'content' => '<h1>Testes Automatizados</h1>
                <p>Testes automatizados são essenciais para garantir a qualidade do código.</p>
                <h2>Tipos de Testes</h2>
                <ul>
                    <li>Unitários - testam unidades isoladas</li>
                    <li>Integração - testam integração entre componentes</li>
                    <li>Funcionais - testam funcionalidades completas</li>
                </ul>
                <h2>PHPUnit</h2>
                <p>PHPUnit é o framework de testes padrão para PHP.</p>
                <pre><code>class MinhaClasseTest extends TestCase {
    public function test_exemplo() {
        $this->assertTrue(true);
    }
}</code></pre>',
                'course_id' => 5,
                'title' => 'Fundamentos de Testes',
                'order' => 2,
            ],
        ];

        foreach ($articles as $articleData) {
            $article = ArticleActivity::create([
                'content' => $articleData['content'],
            ]);

            Activity::create([
                'course_id' => $articleData['course_id'],
                'activity_type_id' => $articleType->id,
                'title' => $articleData['title'],
                'order' => $articleData['order'],
                'activityable_id' => $article->id,
                'activityable_type' => ArticleActivity::class,
            ]);
        }
    }
}