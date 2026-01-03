<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\CertificateController;



// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public course browsing (users can view courses without login)
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);



Route::middleware('auth:sanctum')->group(function () {
    
    // Auth User Routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    
    // Users API - Admin only
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
    
    // Courses API - Create/Update/Delete require Instructor or Admin role
    Route::prefix('courses')->group(function () {
        Route::post('/', [CourseController::class, 'store'])->middleware('role:instructor,admin');
        Route::put('/{id}', [CourseController::class, 'update'])->middleware('role:instructor,admin');
        Route::delete('/{id}', [CourseController::class, 'destroy'])->middleware('role:instructor,admin');
    });
    
    // Lessons API - Instructor/Admin can create, all authenticated can view
    Route::prefix('lessons')->group(function () {
        Route::get('/', [LessonController::class, 'index']);
        Route::post('/', [LessonController::class, 'store'])->middleware('role:instructor,admin');
        Route::get('/{id}', [LessonController::class, 'show']);
        Route::put('/{id}', [LessonController::class, 'update'])->middleware('role:instructor,admin');
        Route::delete('/{id}', [LessonController::class, 'destroy'])->middleware('role:instructor,admin');
    });
    
    // Quizzes API - Instructor/Admin can create, all authenticated can view/take
    Route::prefix('quizzes')->group(function () {
        Route::get('/', [QuizController::class, 'index']);
        Route::post('/', [QuizController::class, 'store'])->middleware('role:instructor,admin');
        Route::get('/{id}', [QuizController::class, 'show']);
        Route::put('/{id}', [QuizController::class, 'update'])->middleware('role:instructor,admin');
        Route::delete('/{id}', [QuizController::class, 'destroy'])->middleware('role:instructor,admin');
    });
    
    // Enrollments API - Students can enroll, Instructors/Admins can manage
    Route::prefix('enrollments')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index']);
        Route::post('/', [EnrollmentController::class, 'store']); // Students can enroll
        Route::get('/{id}', [EnrollmentController::class, 'show']);
        Route::put('/{id}', [EnrollmentController::class, 'update'])->middleware('role:instructor,admin');
        Route::delete('/{id}', [EnrollmentController::class, 'destroy'])->middleware('role:instructor,admin');
    });
    
    // Certificates API - All authenticated users can view their own certificates
    Route::prefix('certificates')->group(function () {
        Route::get('/', [CertificateController::class, 'index']);
        Route::post('/', [CertificateController::class, 'store'])->middleware('role:instructor,admin');
        Route::get('/{id}', [CertificateController::class, 'show']);
        Route::get('/verify/{code}', [CertificateController::class, 'verify']);
        Route::delete('/{id}', [CertificateController::class, 'destroy'])->middleware('role:admin');
    });
    
});

