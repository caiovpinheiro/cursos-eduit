<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed activity types
        $this->call(ActivityTypeSeeder::class);

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@curso-platform.com',
            'password' => bcrypt('password'),
            'cpf' => '00000000000',
            'type' => 'admin',
        ]);

        // Create test student
        User::create([
            'name' => 'Student User',
            'email' => 'student@curso-platform.com',
            'password' => bcrypt('password'),
            'cpf' => '11111111111',
            'type' => 'student',
        ]);

        // Seed courses
        $this->call(CourseSeeder::class);

        // Seed activities
        $this->call(VideoActivitySeeder::class);
        $this->call(ArticleActivitySeeder::class);
        $this->call(QuizActivitySeeder::class);
        $this->call(MiniGameActivitySeeder::class);

        // Seed certificates and enrollments
        $this->call(CertificateSeeder::class);
    }
}
