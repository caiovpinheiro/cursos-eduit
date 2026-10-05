<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\CertificateValidationController;
use App\Http\Controllers\Web\ActivityCompletionController;
use App\Http\Controllers\Web\AssessmentController;
use App\Http\Controllers\Web\CertificatePdfController;
use App\Http\Controllers\Web\CourseEnrollmentController;
use App\Http\Controllers\Web\PasswordResetController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\SessionAuthController;
use App\Livewire\Certificates\ListCertificates;
use App\Livewire\Certificates\ShowCertificate;
use App\Livewire\Courses\ListCourses;
use App\Livewire\Courses\CoursePlayer;
use App\Livewire\Courses\MyCourses;
use App\Livewire\Courses\ShowCourse;
use App\Livewire\Dashboard\HomeDashboard;
use App\Livewire\Profile\ProfilePage;
use App\Livewire\Profile\SettingsPage;
use App\Models\Course;

Route::get('/', function () {
    $featuredCourses = Course::query()
        ->where('is_active', true)
        ->withCount('activities')
        ->latest('id')
        ->limit(3)
        ->get();

    return view('welcome', [
        'featuredCourses' => $featuredCourses,
    ]);
});

Route::get('/auth/redirect', [SocialiteController::class, 'redirectGoogle']);
Route::get('/auth/callback', [SocialiteController::class, 'callbackGoogle']);
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionAuthController::class, 'create'])->name('login');
    Route::post('/login', [SessionAuthController::class, 'store'])
        ->middleware('throttle:web-login')
        ->name('web.login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('web.register.store');
    Route::get('/register/google/complete', [RegisterController::class, 'createGoogleComplete'])
        ->name('web.register.google.complete');
    Route::post('/register/google/complete', [RegisterController::class, 'storeGoogleComplete'])
        ->name('web.register.google.complete.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'createForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'storeForgot'])
        ->middleware('throttle:web-password-recovery')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'createReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'storeReset'])
        ->middleware('throttle:web-password-recovery')
        ->name('password.update');
});
Route::post('/logout', [SessionAuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('web.logout');

Route::get('/certificates/validate/{uuid}', [CertificateValidationController::class, 'validate'])
    ->name('web.certificates.validate');

Route::get('/courses', ListCourses::class)->name('web.courses.index');
Route::get('/courses/{id}', ShowCourse::class)
    ->where('id', '[0-9]+')
    ->name('web.courses.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', HomeDashboard::class)->name('web.dashboard');
    Route::get('/my-courses', MyCourses::class)->name('web.my-courses');
    Route::get('/profile', ProfilePage::class)->name('web.profile');
    Route::get('/settings', SettingsPage::class)->name('web.settings');
    Route::post('/courses/{id}/enroll', CourseEnrollmentController::class)
        ->where('id', '[0-9]+')
        ->name('web.courses.enroll');
    Route::post('/activities/{activityId}/complete', [ActivityCompletionController::class, 'store'])
        ->where('activityId', '[0-9]+')
        ->name('web.activities.complete');
    Route::delete('/activities/{activityId}/complete', [ActivityCompletionController::class, 'destroy'])
        ->where('activityId', '[0-9]+')
        ->name('web.activities.incomplete');
    Route::get('/certificates', ListCertificates::class)->name('web.certificates.index');
    Route::get('/certificates/{id}', ShowCertificate::class)
        ->where('id', '[0-9]+')
        ->name('web.certificates.show');
    Route::get('/certificates/{id}/pdf', [CertificatePdfController::class, 'download'])
        ->where('id', '[0-9]+')
        ->name('web.certificates.pdf');
    Route::get('/certificates/{id}/preview', [CertificatePdfController::class, 'preview'])
        ->where('id', '[0-9]+')
        ->name('web.certificates.preview');
    Route::get('/certificates/{id}/preview-image', [CertificatePdfController::class, 'previewImage'])
        ->where('id', '[0-9]+')
        ->name('web.certificates.preview-image');
    Route::get('/courses/{id}/player', CoursePlayer::class)
        ->where('id', '[0-9]+')
        ->name('web.courses.player');
    Route::post('/assessments/quiz/{quizId}/submit', [AssessmentController::class, 'submitQuiz'])
        ->where('quizId', '[0-9]+')
        ->name('web.assessments.quiz.submit');
    Route::post('/assessments/final-exam/{examId}/submit', [AssessmentController::class, 'submitFinalExam'])
        ->where('examId', '[0-9]+')
        ->name('web.assessments.final-exam.submit');
});