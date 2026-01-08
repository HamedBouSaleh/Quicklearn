<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Certificate;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Get enrollments
        $enrollments = Enrollment::where('user_id', $userId)->get();
        $enrolledCourseIds = $enrollments->pluck('course_id');

        // Calculate stats
        $enrolledCount = $enrollments->count();
        $completedCount = $enrollments->where('progress_percentage', 100)->count();
        $certificatesCount = Certificate::where('user_id', $userId)->count();

        // Calculate total hours from completed lessons
        $completedLessons = LessonCompletion::where('user_id', $userId)->count();
        $totalLessons = \App\Models\Lesson::whereIn('course_id', $enrolledCourseIds)->count();
        $totalHours = round($completedLessons * 0.5); // Estimate 30 min per lesson

        // Calculate overall progress
        $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Calculate quiz average
        $quizAttempts = QuizAttempt::where('user_id', $userId)->get();
        $quizAverage = $quizAttempts->count() > 0 ? round($quizAttempts->avg('score')) : 0;

        // Learning streak (simplified - count days with activity)
        $learningStreak = 7; // Placeholder

        // Get continue lesson (last incomplete lesson from enrolled courses)
        $continueLesson = null;
        foreach ($enrollments as $enrollment) {
            if ($enrollment->progress_percentage < 100) {
                $course = Course::find($enrollment->course_id);
                $lessons = $course->lessons;
                
                foreach ($lessons as $lesson) {
                    $isCompleted = LessonCompletion::where('lesson_id', $lesson->id)
                        ->where('user_id', $userId)
                        ->exists();
                    
                    if (!$isCompleted) {
                        $continueLesson = [
                            'id' => $lesson->id,
                            'course_id' => $course->id,
                            'course_title' => $course->title,
                            'lesson_title' => $lesson->title,
                            'progress' => $enrollment->progress_percentage
                        ];
                        break 2;
                    }
                }
            }
        }

        // Get enrolled courses
        $enrolledCourses = Course::whereIn('id', $enrolledCourseIds)
            ->get()
            ->map(function($course) use ($enrollments) {
                $enrollment = $enrollments->firstWhere('course_id', $course->id);
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'thumbnail_url' => $course->thumbnail_url,
                    'progress' => $enrollment ? $enrollment->progress_percentage : 0
                ];
            });

        // Get recent certificates
        $recentCertificates = Certificate::where('user_id', $userId)
            ->with('course')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($cert) {
                return [
                    'id' => $cert->id,
                    'course_title' => $cert->course->title,
                    'issued_date' => $cert->created_at->format('M d, Y')
                ];
            });

        return Inertia::render('Student/Dashboard', [
            'enrolledCourses' => $enrolledCount,
            'completedCourses' => $completedCount,
            'hoursLearned' => $totalHours,
            'avgProgress' => $overallProgress . '%',
            'recentCourses' => $enrolledCourses->slice(0, 3)->map(function($course) {
                return [
                    'id' => $course['id'],
                    'title' => $course['title'],
                    'instructor' => 'Your Instructor',
                    'progress' => $course['progress']
                ];
            }),
            'recommendedCourses' => Course::where('is_published', true)
                ->limit(3)
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'instructor' => $course->created_by ? \App\Models\User::find($course->created_by)->name : 'Expert Instructor',
                        'lessons' => $course->lessons_count ?? 0
                    ];
                })
        ]);
    }
}
