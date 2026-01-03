<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $instructorId = auth()->id();

        // Get instructor's courses
        $courses = Course::where('created_by', $instructorId)
            ->withCount(['lessons', 'enrollments'])
            ->get();

        // Calculate stats
        $totalCourses = $courses->count();
        $totalStudents = $courses->sum('enrollments_count');
        $totalLessons = $courses->sum('lessons_count');
        
        $totalQuizzes = Quiz::whereIn('course_id', $courses->pluck('id'))->count();

        // Get courses for display
        $coursesData = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'students_count' => $course->enrollments_count,
                'lessons_count' => $course->lessons_count,
                'created_at' => $course->created_at->format('M d, Y')
            ];
        })->take(5);

        // Recent activity
        $courseIds = $courses->pluck('id');
        $recentActivity = [];
        
        if ($courseIds->isNotEmpty()) {
            $recentActivity = Enrollment::whereIn('course_id', $courseIds)
                ->with(['user', 'course'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'title' => "{$enrollment->user->name} enrolled in {$enrollment->course->title}",
                        'date' => $enrollment->created_at ? $enrollment->created_at->diffForHumans() : 'Recently'
                    ];
                });
        }

        return Inertia::render('Instructor/Dashboard', [
            'stats' => [
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'total_lessons' => $totalLessons,
                'total_quizzes' => $totalQuizzes
            ],
            'courses' => $coursesData,
            'recentActivity' => $recentActivity
        ]);
    }
}
