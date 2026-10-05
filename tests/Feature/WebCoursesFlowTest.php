<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\ArticleActivity;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuizActivity;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class WebCoursesFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_public_courses_routes_but_not_private_routes(): void
    {
        $course = Course::create([
            'title' => 'Curso Web',
            'description' => 'Descricao',
            'workload' => 10,
            'is_active' => true,
        ]);

        $this->get('/courses')->assertOk();
        $this->get('/courses/' . $course->id)->assertOk();
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/certificates')->assertRedirect('/login');
    }

    public function test_login_page_is_accessible_and_can_authenticate_with_email_password(): void
    {
        $user = $this->createStudentUser();

        $this->get('/login')
            ->assertOk()
            ->assertSee('Entrar');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');
    }

    public function test_register_page_is_accessible_and_can_create_account_with_email_password(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Crie sua conta');

        $this->post('/register', [
            'name' => 'Novo Aluno',
            'email' => 'novo' . uniqid() . '@teste.com',
            'cpf' => (string) random_int(10000000000, 99999999999),
            'phone' => '11999999999',
            'education_level' => 'superior_completo',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');
    }

    public function test_google_signup_second_step_requires_pending_session_data(): void
    {
        $this->get('/register/google/complete')
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Sua sessao de cadastro com Google expirou. Tente novamente.',
            ]);
    }

    public function test_google_signup_second_step_can_finalize_account_creation(): void
    {
        $email = 'google' . uniqid() . '@teste.com';

        $response = $this->withSession([
            'pending_google_signup' => [
                'name' => 'Aluno Google',
                'email' => $email,
                'google_id' => 'google_' . uniqid(),
            ],
        ])->post('/register/google/complete', [
            'cpf' => (string) random_int(10000000000, 99999999999),
            'phone' => '11988887777',
            'education_level' => 'medio_completo',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Recuperar senha');
    }

    public function test_traditional_account_can_request_password_reset_link(): void
    {
        $user = $this->createStudentUser();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHas('status');
    }

    public function test_google_only_account_is_blocked_from_password_recovery(): void
    {
        $googleUser = User::create([
            'name' => 'Aluno Google',
            'email' => 'google-only' . uniqid() . '@teste.com',
            'google_id' => 'gid_' . uniqid(),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'type' => 'student',
        ]);

        $this->post('/forgot-password', [
            'email' => $googleUser->email,
        ])->assertSessionHasErrors([
            'email' => 'Esta conta usa login com Google. Entre com Google para continuar.',
        ]);
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        $user = $this->createStudentUser();

        for ($i = 0; $i < 6; $i++) {
            $response = $this->from('/login')->post('/login', [
                'email' => $user->email,
                'password' => 'senha-incorreta',
            ]);
        }

        $response->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Muitas tentativas em pouco tempo. Aguarde um momento e tente novamente.',
            ]);
    }

    public function test_login_is_throttled_by_hourly_window(): void
    {
        $user = $this->createStudentUser();

        for ($batch = 0; $batch < 4; $batch++) {
            for ($i = 0; $i < 5; $i++) {
                $this->from('/login')->post('/login', [
                    'email' => $user->email,
                    'password' => 'senha-incorreta',
                ]);
            }

            if ($batch < 3) {
                $this->travel(61)->seconds();
            }
        }

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'senha-incorreta',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Muitas tentativas em pouco tempo. Aguarde um momento e tente novamente.',
            ]);
    }

    public function test_forgot_password_is_throttled_after_too_many_attempts(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->from('/forgot-password')->post('/forgot-password', [
                'email' => 'naoexiste@teste.com',
            ]);
        }

        $response->assertRedirect('/forgot-password')
            ->assertSessionHasErrors([
                'email' => 'Muitas tentativas em pouco tempo. Aguarde um momento e tente novamente.',
            ]);
    }

    public function test_reset_password_is_throttled_after_too_many_attempts(): void
    {
        $user = $this->createStudentUser();

        for ($i = 0; $i < 4; $i++) {
            $response = $this->from('/reset-password/token-fake')->post('/reset-password', [
                'token' => 'token-fake',
                'email' => $user->email,
                'password' => 'nova-senha-123',
                'password_confirmation' => 'nova-senha-123',
            ]);
        }

        $response->assertRedirect('/reset-password/token-fake')
            ->assertSessionHasErrors([
                'email' => 'Muitas tentativas em pouco tempo. Aguarde um momento e tente novamente.',
            ]);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = $this->createStudentUser();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect('/login');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password-123',
        ])->assertRedirect('/dashboard');
    }

    public function test_security_log_contains_structured_audit_context(): void
    {
        $user = User::create([
            'name' => 'Aluno Google',
            'email' => 'google-only' . uniqid() . '@teste.com',
            'google_id' => 'gid_' . uniqid(),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'type' => 'student',
        ]);

        $logPath = storage_path('logs/security.log');
        File::delete($logPath);

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHasErrors('email');

        $this->assertTrue(File::exists($logPath));

        $contents = (string) File::get($logPath);
        $this->assertStringContainsString('auth.password_recovery_blocked_google_only', $contents);
        $this->assertStringContainsString('request_id', $contents);
        $this->assertStringContainsString('route', $contents);
        $this->assertStringContainsString('email_hash', $contents);
    }

    public function test_authenticated_student_can_access_dashboard(): void
    {
        $user = $this->createStudentUser();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Olá')
            ->assertSee('Meus Cursos')
            ->assertSee('Ações Rápidas');
    }

    public function test_courses_page_allows_search_and_sort_controls(): void
    {
        $user = $this->createStudentUser();

        Course::create([
            'title' => 'Laravel para iniciantes',
            'description' => 'Descricao',
            'workload' => 12,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/courses')
            ->assertOk()
            ->assertSee('Buscar')
            ->assertSee('Ordenar por')
            ->assertSee('Direcao')
            ->assertSee('Laravel para iniciantes');
    }

    public function test_courses_page_accepts_query_string_filters(): void
    {
        $user = $this->createStudentUser();

        Course::create([
            'title' => 'Curso de Laravel',
            'description' => 'Descricao',
            'workload' => 10,
            'is_active' => true,
        ]);

        Course::create([
            'title' => 'Curso de Java',
            'description' => 'Descricao',
            'workload' => 20,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/courses?search=Laravel&sortBy=workload&sortDirection=desc')
            ->assertOk()
            ->assertSee('Curso de Laravel')
            ->assertDontSee('Curso de Java');
    }

    public function test_authenticated_student_can_view_courses_pages(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Blade',
            'description' => 'Descricao',
            'workload' => 12,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/courses')
            ->assertOk()
            ->assertSee('Curso Blade');

        $this->actingAs($user)
            ->get('/courses/' . $course->id)
            ->assertOk()
            ->assertSee('Matricular-se');
    }

    public function test_authenticated_student_can_enroll_via_web_route(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Matricula',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post('/courses/' . $course->id . '/enroll')
            ->assertRedirect('/courses/' . $course->id);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get('/courses/' . $course->id)
            ->assertOk()
            ->assertSee('Seu progresso');
    }

    public function test_authenticated_student_can_complete_activity_from_web(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Atividades',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
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

        $this->actingAs($user)
            ->post('/activities/' . $activity->id . '/complete')
            ->assertRedirect('/courses/' . $course->id);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'completed' => true,
        ]);
    }

    public function test_completing_activity_from_player_redirects_back_to_player(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Player Redirect',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
        ]);

        $activityType = ActivityType::create(['name' => 'article']);
        $article = ArticleActivity::create([
            'description' => 'Desc',
            'content_richtext' => 'Conteudo',
        ]);

        $activity = Activity::create([
            'course_id' => $course->id,
            'activity_type_id' => $activityType->id,
            'title' => 'Aula player',
            'order' => 1,
            'activityable_id' => $article->id,
            'activityable_type' => ArticleActivity::class,
        ]);

        $this->actingAs($user)
            ->post('/activities/' . $activity->id . '/complete', [
                'redirect_to' => 'player',
            ])
            ->assertRedirect('/courses/' . $course->id . '/player?activity=' . $activity->id);
    }

    public function test_authenticated_student_can_access_course_player_page(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Player',
            'description' => 'Descricao',
            'workload' => 10,
            'is_active' => true,
        ]);

        UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
        ]);

        $activityType = ActivityType::create(['name' => 'article']);
        $article = ArticleActivity::create([
            'description' => 'Desc',
            'content_richtext' => 'Conteudo',
        ]);

        Activity::create([
            'course_id' => $course->id,
            'activity_type_id' => $activityType->id,
            'title' => 'Modulo 1',
            'order' => 1,
            'activityable_id' => $article->id,
            'activityable_type' => ArticleActivity::class,
        ]);

        $this->actingAs($user)
            ->get('/courses/' . $course->id . '/player')
            ->assertOk()
            ->assertSee('Progresso do curso')
            ->assertSee('Modulo 1');
    }

    public function test_authenticated_student_can_submit_quiz_from_web_player(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Quiz Web',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        UserCourse::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
        ]);

        $activityType = ActivityType::create(['name' => 'quiz']);
        $quiz = QuizActivity::create([
            'description' => 'Quiz modulo 1',
            'duration_minutes' => 10,
        ]);

        $activity = Activity::create([
            'course_id' => $course->id,
            'activity_type_id' => $activityType->id,
            'title' => 'Quiz 1',
            'order' => 1,
            'activityable_id' => $quiz->id,
            'activityable_type' => QuizActivity::class,
        ]);

        $question = Question::create([
            'questionable_id' => $quiz->id,
            'questionable_type' => QuizActivity::class,
            'statement' => 'Qual a letra correta?',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'a',
            'order' => 1,
        ]);

        $this->actingAs($user)
            ->post('/assessments/quiz/' . $quiz->id . '/submit', [
                'answers' => [
                    $question->id => 'a',
                ],
            ])
            ->assertRedirect('/courses/' . $course->id . '/player?activity=' . $activity->id);

        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $user->id,
            'questionable_id' => $quiz->id,
            'questionable_type' => QuizActivity::class,
            'passed' => true,
        ]);
    }

    public function test_authenticated_student_can_access_certificates_pages(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso Certificado',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get('/certificates')
            ->assertOk()
            ->assertSee('Meus certificados')
            ->assertSee('Meus Certificados', false)
            ->assertSee('/certificates/'.$certificate->id.'/preview-image', false);

        $this->actingAs($user)
            ->get('/certificates/' . $certificate->id)
            ->assertOk()
            ->assertSee('Conquista desbloqueada')
            ->assertSee('/certificates/'.$certificate->id.'/preview-image', false);

        $this->actingAs($user)
            ->get('/certificates/'.$certificate->id.'/preview')
            ->assertOk()
            ->assertSee($certificate->uuid);
    }

    public function test_authenticated_student_can_download_own_certificate_pdf(): void
    {
        $user = $this->createStudentUser();
        $course = Course::create([
            'title' => 'Curso PDF',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/certificates/'.$certificate->id.'/pdf');

        $response->assertOk();
        $this->assertStringContainsString('pdf', strtolower($response->headers->get('content-type', '')));
    }

    public function test_student_cannot_download_another_users_certificate_pdf(): void
    {
        $owner = $this->createStudentUser();
        $otherStudent = $this->createStudentUser();

        $course = Course::create([
            'title' => 'Curso PDF outro',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $owner->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($otherStudent)
            ->get('/certificates/'.$certificate->id.'/pdf')
            ->assertForbidden();
    }

    public function test_student_cannot_access_certificate_from_another_user(): void
    {
        $owner = $this->createStudentUser();
        $otherStudent = $this->createStudentUser();

        $course = Course::create([
            'title' => 'Curso Privado',
            'description' => 'Descricao',
            'workload' => 8,
            'is_active' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $owner->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($otherStudent)
            ->get('/certificates/' . $certificate->id)
            ->assertForbidden();
    }

    private function createStudentUser(): User
    {
        return User::create([
            'name' => 'Aluno Teste',
            'email' => 'aluno' . uniqid() . '@teste.com',
            'password' => bcrypt('password'),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'type' => 'student',
        ]);
    }
}
