<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\ArticleActivity;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiRoutesRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_courses_index_keeps_working_for_authenticated_student(): void
    {
        $user = $this->createStudentUser();
        Sanctum::actingAs($user);

        Course::create([
            'title' => 'Curso API',
            'description' => 'Descricao API',
            'workload' => 15,
            'is_active' => true,
        ]);

        $this->getJson('/api/courses')
            ->assertOk()
            ->assertJsonStructure([
                'current_page',
                'data',
                'first_page_url',
                'last_page',
                'per_page',
                'total',
            ]);
    }

    public function test_api_public_courses_still_accessible_without_authentication(): void
    {
        Course::create([
            'title' => 'Curso Publico',
            'description' => 'Descricao publica',
            'workload' => 6,
            'is_active' => true,
        ]);

        $this->getJson('/api/public/courses')
            ->assertOk()
            ->assertJsonStructure([
                'courses',
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_sanctum_token_expiration_is_configured_to_24_hours(): void
    {
        $this->assertSame(1440, Config::get('sanctum.expiration'));
    }

    public function test_api_error_contract_includes_code_for_unauthenticated_requests(): void
    {
        $this->getJson('/api/courses')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
                'code' => 'unauthenticated',
            ]);
    }

    public function test_complete_activity_returns_standard_error_contract_when_not_enrolled(): void
    {
        $user = $this->createStudentUser();
        Sanctum::actingAs($user);

        $course = Course::create([
            'title' => 'Curso Sem Matricula',
            'description' => 'Descricao',
            'workload' => 10,
            'is_active' => true,
        ]);

        $activityType = ActivityType::create(['name' => 'article']);
        $article = ArticleActivity::create([
            'description' => 'Desc',
            'content_richtext' => 'Conteudo',
        ]);

        $activity = Activity::create([
            'course_id' => $course->id,
            'activity_type_id' => $activityType->id,
            'title' => 'Aula 1',
            'order' => 1,
            'activityable_id' => $article->id,
            'activityable_type' => ArticleActivity::class,
        ]);

        $this->postJson('/api/activities/' . $activity->id . '/complete')
            ->assertStatus(403)
            ->assertJson([
                'message' => 'You are not enrolled in this course',
                'code' => 'not_enrolled',
            ])
            ->assertJsonStructure(['message', 'errors', 'code']);
    }

    private function createStudentUser(): User
    {
        return User::create([
            'name' => 'Aluno API',
            'email' => 'api' . uniqid() . '@teste.com',
            'password' => bcrypt('password'),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'type' => 'student',
        ]);
    }
}
