<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VideoActivity;
use App\Models\Activity;
use App\Models\ActivityType;

class VideoActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videoType = ActivityType::where('name', 'video')->first();
        
        $videos = [
            [
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'duration' => 212, // 3:32
                'course_id' => 1,
                'title' => 'Introdução ao Laravel',
                'order' => 1,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example1',
                'duration' => 450, // 7:30
                'course_id' => 1,
                'title' => 'Configuração do Ambiente',
                'order' => 2,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example2',
                'duration' => 380, // 6:20
                'course_id' => 1,
                'title' => 'Rotas e Controllers',
                'order' => 3,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example3',
                'duration' => 520, // 8:40
                'course_id' => 2,
                'title' => 'PHP 8+ Novidades',
                'order' => 1,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example4',
                'duration' => 420, // 7:00
                'course_id' => 2,
                'title' => 'Programação Orientada a Objetos',
                'order' => 2,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example5',
                'duration' => 600, // 10:00
                'course_id' => 3,
                'title' => 'Criando sua Primeira API',
                'order' => 1,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example6',
                'duration' => 480, // 8:00
                'course_id' => 3,
                'title' => 'Autenticação e Autorização',
                'order' => 2,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example7',
                'duration' => 360, // 6:00
                'course_id' => 4,
                'title' => 'Introdução ao Docker',
                'order' => 1,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example8',
                'duration' => 540, // 9:00
                'course_id' => 4,
                'title' => 'Docker Compose na Prática',
                'order' => 2,
            ],
            [
                'url' => 'https://www.youtube.com/watch?v=example9',
                'duration' => 420, // 7:00
                'course_id' => 5,
                'title' => 'Testes Unitários com PHPUnit',
                'order' => 1,
            ],
        ];

        foreach ($videos as $videoData) {
            $video = VideoActivity::create([
                'url' => $videoData['url'],
                'duration' => $videoData['duration'],
            ]);

            Activity::create([
                'course_id' => $videoData['course_id'],
                'activity_type_id' => $videoType->id,
                'title' => $videoData['title'],
                'order' => $videoData['order'],
                'activityable_id' => $video->id,
                'activityable_type' => VideoActivity::class,
            ]);
        }
    }
}