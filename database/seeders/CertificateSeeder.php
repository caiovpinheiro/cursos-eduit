<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\UserCourse;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Simular que o student completou alguns cursos
        $student = User::where('email', 'student@curso-platform.com')->first();
        
        if ($student) {
            // Matricular o student em alguns cursos
            $courses = Course::where('is_active', true)->take(3)->get();
            
            foreach ($courses as $index => $course) {
                // Criar matrícula
                UserCourse::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'progress' => 100,
                    'completed_at' => now()->subDays(30 - ($index * 5)), // Completado há alguns dias
                ]);

                // Criar certificado
                $certificate = Certificate::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'issue_date' => now()->subDays(30 - ($index * 5)),
                ]);
            }
        }

        // Criar alguns certificados para outros usuários fictícios
        $fictitiousUsers = [
            [
                'name' => 'João Silva',
                'email' => 'joao@exemplo.com',
                'cpf' => '33333333333',
                'type' => 'student',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@exemplo.com',
                'cpf' => '44444444444',
                'type' => 'student',
            ],
        ];

        foreach ($fictitiousUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password'),
                'cpf' => $userData['cpf'],
                'type' => $userData['type'],
            ]);

            // Matricular em um curso e completar
            $course = Course::where('is_active', true)->first();
            
            if ($course) {
                UserCourse::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'progress' => 100,
                    'completed_at' => now()->subDays(15),
                ]);

                $certificate = Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'issue_date' => now()->subDays(15),
                ]);
            }
        }
    }
}