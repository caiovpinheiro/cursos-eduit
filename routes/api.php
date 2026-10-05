<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\CertificateValidationController;
use App\Http\Controllers\ExternalCustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public certificate validation
Route::get('/certificates/validate/{uuid}', [CertificateValidationController::class, 'validate']);

// Public courses routes
Route::prefix('public')->group(function () {
    Route::get('/courses', [PublicCourseController::class, 'index']);
    Route::get('/courses/categories', [PublicCourseController::class, 'categories']);
    Route::get('/courses/difficulty-levels', [PublicCourseController::class, 'difficultyLevels']);
    Route::get('/courses/{id}', [PublicCourseController::class, 'show'])
        ->where('id', '[0-9]+'); // Garante que ID seja numérico
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'changePassword']);
    Route::delete('/auth/account', [AuthController::class, 'deleteAccount']);
    
    // Courses (unified - authorization via policies)
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/enrolled', [CourseController::class, 'enrolled']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->where('id', '[0-9]+');
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])->where('id', '[0-9]+');
    
    // Activities (unified - authorization via policies)
    Route::get('/courses/{courseId}/activities', [ActivityController::class, 'index'])
        ->where('courseId', '[0-9]+');
    Route::post('/courses/{courseId}/activities', [ActivityController::class, 'store'])
        ->where('courseId', '[0-9]+');
    Route::put('/courses/{courseId}/activities/reorder', [ActivityController::class, 'reorder'])
        ->where('courseId', '[0-9]+');
    Route::get('/activities/{id}', [ActivityController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/activities/{id}', [ActivityController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->where('id', '[0-9]+');
    
    // Questions (unified - authorization via policies)
    Route::get('/{type}/{id}/questions', [QuestionController::class, 'index'])
        ->where('type', 'quiz|final_exam');
    Route::post('/{type}/{id}/questions', [QuestionController::class, 'store'])
        ->where('type', 'quiz|final_exam');
    Route::get('/questions/{questionId}', [QuestionController::class, 'show']);
    Route::put('/questions/{questionId}', [QuestionController::class, 'update']);
    Route::delete('/questions/{questionId}', [QuestionController::class, 'destroy']);
    Route::put('/{type}/{id}/questions/reorder', [QuestionController::class, 'reorder'])
        ->where('type', 'quiz|final_exam');
    
    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::get('/certificates/{id}', [CertificateController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/certificates/course/{courseId}', [CertificateController::class, 'getByCourse'])->where('courseId', '[0-9]+');
    
    // User Activity Progress
    Route::post('/activities/{activityId}/complete', [UserActivityController::class, 'complete'])->where('activityId', '[0-9]+');
    Route::delete('/activities/{activityId}/complete', [UserActivityController::class, 'incomplete'])->where('activityId', '[0-9]+');
    Route::get('/courses/{courseId}/completed-activities', [UserActivityController::class, 'getCompletedActivities'])->where('courseId', '[0-9]+');
    Route::get('/courses/{courseId}/progress', [UserActivityController::class, 'getCourseProgress'])->where('courseId', '[0-9]+');
    
    // Quiz and Final Exam Submissions
    Route::post('/quiz/{quizId}/submit', [QuizAttemptController::class, 'submitQuiz'])->where('quizId', '[0-9]+');
    Route::post('/final_exam/{examId}/submit', [QuizAttemptController::class, 'submitFinalExam'])->where('examId', '[0-9]+');
    Route::get('/quiz/{quizId}/attempts', [QuizAttemptController::class, 'getQuizAttempts'])->where('quizId', '[0-9]+');
    Route::get('/final_exam/{examId}/attempts', [QuizAttemptController::class, 'getFinalExamAttempts'])->where('examId', '[0-9]+');
    
    // External Customers (for CRON sync and admin management)
    Route::post('/external-customers/bulk-sync', [ExternalCustomerController::class, 'bulkSync']);
    Route::get('/external-customers', [ExternalCustomerController::class, 'index']);
    Route::post('/external-customers/check', [ExternalCustomerController::class, 'check']);
});