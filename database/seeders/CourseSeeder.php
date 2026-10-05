<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Laravel do Zero ao Avançado',
                'description' => 'Aprenda Laravel desde o básico até conceitos avançados, incluindo APIs RESTful, autenticação, testes e deploy.',
                'short_description' => 'Curso completo de Laravel com projetos práticos e boas práticas de desenvolvimento.',
                'long_description' => '<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=600&fit=crop',
                'workload' => 60,
                'modules_count' => 12,
                'difficulty_level' => 'avancado',
                'category' => 'programacao',
                'price' => 299.90,
                'promotional_price' => 199.90,
                'discount_percentage' => 33,
                'is_active' => true,
            ],
            [
                'title' => 'PHP Moderno e Boas Práticas',
                'description' => 'Domine PHP 8+ com as melhores práticas, programação orientada a objetos e padrões de design.',
                'short_description' => 'Aprenda PHP 8+ com foco em código limpo, performance e manutenibilidade.',
                'long_description' => '<h2>Conteúdo do curso:</h2><ul><li>PHP 8+ Features</li><li>OOP Avançado</li><li>Padrões de Design</li><li>PSR Standards</li><li>Performance e Otimização</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1599507593354-2b6d036eab4f?w=800&h=600&fit=crop',
                'workload' => 40,
                'modules_count' => 8,
                'difficulty_level' => 'intermediario',
                'category' => 'programacao',
                'price' => 199.90,
                'is_active' => true,
            ],
            [
                'title' => 'Desenvolvimento de APIs RESTful',
                'description' => 'Crie APIs robustas e escaláveis usando Laravel, incluindo documentação, testes e versionamento.',
                'short_description' => 'Desenvolva APIs profissionais com Laravel, incluindo autenticação, validação e documentação.',
                'long_description' => '<h2>Módulos do curso:</h2><ul><li>Fundamentos de APIs REST</li><li>Laravel API Resources</li><li>Autenticação JWT</li><li>Validação e Tratamento de Erros</li><li>Documentação com Swagger</li><li>Testes de API</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=600&fit=crop',
                'workload' => 35,
                'modules_count' => 10,
                'difficulty_level' => 'intermediario',
                'category' => 'programacao',
                'price' => 249.90,
                'promotional_price' => 174.90,
                'discount_percentage' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Docker para Desenvolvedores',
                'description' => 'Aprenda a containerizar aplicações, gerenciar ambientes e deploy com Docker e Docker Compose.',
                'short_description' => 'Domine Docker e containerização para desenvolvimento e produção.',
                'long_description' => '<h2>O que você vai aprender:</h2><ul><li>Conceitos de Containerização</li><li>Dockerfile e Docker Compose</li><li>Volumes e Networks</li><li>Orquestração com Docker Swarm</li><li>Deploy em Produção</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1605379399642-870262d3d051?w=800&h=600&fit=crop',
                'workload' => 25,
                'modules_count' => 6,
                'difficulty_level' => 'iniciante',
                'category' => 'tecnologia',
                'price' => 149.90,
                'is_active' => true,
            ],
            [
                'title' => 'Testes Automatizados com PHPUnit',
                'description' => 'Implemente testes unitários, de integração e funcionais para garantir qualidade do código.',
                'short_description' => 'Aprenda a escrever testes eficazes e manter a qualidade do seu código PHP.',
                'long_description' => '<h2>Conteúdo:</h2><ul><li>Fundamentos de Testes</li><li>PHPUnit Avançado</li><li>Mocks e Stubs</li><li>Testes de Integração</li><li>CI/CD com Testes</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=600&fit=crop',
                'workload' => 30,
                'modules_count' => 7,
                'difficulty_level' => 'intermediario',
                'category' => 'programacao',
                'price' => 179.90,
                'is_active' => true,
            ],
            [
                'title' => 'MySQL e Bancos de Dados Relacionais',
                'description' => 'Domine consultas SQL, otimização de performance e design de bancos de dados.',
                'short_description' => 'Aprenda MySQL do básico ao avançado com foco em performance e boas práticas.',
                'long_description' => '<h2>Módulos:</h2><ul><li>Fundamentos de SQL</li><li>Design de Banco de Dados</li><li>Índices e Performance</li><li>Transações e ACID</li><li>Backup e Recuperação</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800&h=600&fit=crop',
                'workload' => 45,
                'modules_count' => 9,
                'difficulty_level' => 'intermediario',
                'category' => 'banco_dados',
                'price' => 219.90,
                'is_active' => true,
            ],
            [
                'title' => 'Git e Controle de Versão',
                'description' => 'Aprenda Git desde o básico até workflows avançados com GitHub, GitLab e Bitbucket.',
                'short_description' => 'Domine Git e controle de versão para trabalhar em equipe de forma eficiente.',
                'long_description' => '<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Git</li><li>Branching e Merging</li><li>GitHub e GitLab</li><li>Workflows em Equipe</li><li>Resolução de Conflitos</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1556075798-4825dfaaf498?w=800&h=600&fit=crop',
                'workload' => 20,
                'modules_count' => 5,
                'difficulty_level' => 'iniciante',
                'category' => 'tecnologia',
                'price' => 0.00, // Curso gratuito
                'is_active' => true,
            ],
            [
                'title' => 'Liderança e Gestão de Equipes',
                'description' => 'Desenvolva habilidades de liderança e aprenda a gerenciar equipes de desenvolvimento.',
                'short_description' => 'Aprenda a liderar equipes de desenvolvimento com eficiência e motivação.',
                'long_description' => '<h2>Conteúdo:</h2><ul><li>Fundamentos de Liderança</li><li>Gestão de Pessoas</li><li>Comunicação Eficaz</li><li>Metodologias Ágeis</li><li>Resolução de Conflitos</li></ul>',
                'cover_image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=600&fit=crop',
                'workload' => 30,
                'modules_count' => 6,
                'difficulty_level' => 'intermediario',
                'category' => 'lideranca',
                'price' => 399.90,
                'promotional_price' => 299.90,
                'discount_percentage' => 25,
                'is_active' => true,
            ],
            [
                'title' => 'Curso Descontinuado',
                'description' => 'Este curso foi descontinuado e não está mais disponível.',
                'short_description' => 'Curso descontinuado.',
                'workload' => 15,
                'modules_count' => 3,
                'difficulty_level' => 'iniciante',
                'category' => 'outros',
                'price' => 99.90,
                'is_active' => false,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}