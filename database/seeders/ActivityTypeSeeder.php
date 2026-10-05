<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityTypes = [
            ['name' => 'video'],
            ['name' => 'article'],
            ['name' => 'quiz'],
            ['name' => 'mini_game'],
            ['name' => 'embed_content'],
            ['name' => 'support_material'],
            ['name' => 'final_exam'],
        ];

        foreach ($activityTypes as $type) {
            ActivityType::firstOrCreate($type);
        }
    }
}