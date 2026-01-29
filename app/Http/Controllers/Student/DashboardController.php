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
        // Count completed courses (all lessons done)
        $completedCount = 0;
        foreach ($enrollments as $enrollment) {
            $totalLessons = \App\Models\Lesson::where('course_id', $enrollment->course_id)->count();
            if ($totalLessons > 0) {
                $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', function($query) use ($enrollment) {
                    $query->select('id')->from('lessons')->where('course_id', $enrollment->course_id);
                })->where('user_id', $enrollment->user_id)->count();
                if ($completedLessons === $totalLessons) {
                    $completedCount++;
                }
            }
        }
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
            // Calculate real-time progress
            $totalLessons = \App\Models\Lesson::where('course_id', $enrollment->course_id)->count();
            $completedLessons = 0;
            $courseProgress = 0;
            if ($totalLessons > 0) {
                $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', function($query) use ($enrollment) {
                    $query->select('id')->from('lessons')->where('course_id', $enrollment->course_id);
                })->where('user_id', $userId)->count();
                $courseProgress = round(($completedLessons / $totalLessons) * 100);
            }
            
            if ($courseProgress < 100) {
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
                            'progress' => $courseProgress
                        ];
                        break 2;
                    }
                }
            }
        }

        // Get enrolled courses
        $enrolledCourses = Course::whereIn('id', $enrolledCourseIds)
            ->get()
            ->map(function($course) use ($enrollments, $userId) {
                $enrollment = $enrollments->firstWhere('course_id', $course->id);
                // Calculate real-time progress
                $progress = 0;
                if ($enrollment) {
                    $totalLessons = \App\Models\Lesson::where('course_id', $course->id)->count();
                    if ($totalLessons > 0) {
                        $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', function($query) use ($course) {
                            $query->select('id')->from('lessons')->where('course_id', $course->id);
                        })->where('user_id', $userId)->count();
                        $progress = round(($completedLessons / $totalLessons) * 100);
                    }
                }
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'thumbnail_url' => $course->thumbnail_url,
                    'progress' => $progress
                ];
            });

        // Get recent certificates
        $recentCertificates = Certificate::where('user_id', $userId)
            ->with('course')
            ->latest('generated_at')
            ->take(3)
            ->get()
            ->map(function($cert) {
                return [
                    'id' => $cert->id,
                    'course_title' => $cert->course->title,
                    'issued_date' => $cert->generated_at->format('M d, Y')
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
                ->whereNotIn('id', $enrolledCourseIds)
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
