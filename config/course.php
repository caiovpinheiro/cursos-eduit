<?php

return [
    'cover_upload' => [
        'disk' => env('COURSE_COVER_DISK', 'public'),
        'dir' => env('COURSE_COVER_DIR', 'course-covers'),
        'max_kb' => (int) env('COURSE_COVER_MAX_KB', 2048),
        'mimes' => env('COURSE_COVER_MIMES', 'jpg,jpeg,png,webp'),
    ],
];

