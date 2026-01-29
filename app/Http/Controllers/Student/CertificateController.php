<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $certificates = Certificate::where('user_id', $userId)
            ->with('course')
            ->latest('generated_at')
            ->get()
            ->map(function($cert) {
                return [
                    'id' => $cert->id,
                    'course_title' => $cert->course->title ?? 'Course',
                    'completion_date' => $cert->generated_at ? $cert->generated_at->format('M d, Y') : 'N/A'
                ];
            });

        return Inertia::render('Student/Certificates/Index', [
            'certificates' => $certificates
        ]);
    }

    public function show($id)
    {
        $certificate = Certificate::with(['course.lessons', 'course.creator', 'user'])->findOrFail($id);
        
        $userId = $certificate->user_id;
        $courseId = $certificate->course_id;
        
        // Get actual lessons completed count
        $lessonsCompleted = \App\Models\LessonCompletion::whereIn('lesson_id', function($query) use ($courseId) {
            $query->select('id')
                ->from('lessons')
                ->where('course_id', $courseId);
        })->where('user_id', $userId)->count();
        
        // Get quizzes passed count and calculate average score
        $quizAttempts = \App\Models\QuizAttempt::whereIn('quiz_id', function($query) use ($courseId) {
            $query->select('id')
                ->from('quizzes')
                ->where('course_id', $courseId);
        })
        ->where('user_id', $userId)
        ->whereNotNull('completed_at')
        ->get();
        
        // Count unique quizzes passed
        $quizzesPassed = $quizAttempts->filter(function($attempt) {
            $quiz = \App\Models\Quiz::find($attempt->quiz_id);
            return $quiz && $attempt->percentage >= $quiz->passing_score;
        })->unique('quiz_id')->count();
        
        // Calculate average percentage from all completed attempts
        $averageScore = $quizAttempts->avg('percentage') ?? 0;
        
        // Get instructor name from course creator
        $instructorName = $certificate->course->creator->name ?? 'Instructor';
        
        return Inertia::render('Certificates/Show', [
            'certificate' => [
                'id' => $certificate->id,
                'student_name' => $certificate->user->name ?? auth()->user()->name ?? 'Student Name',
                'course_title' => $certificate->course->title ?? 'Course',
                'issued_date' => $certificate->generated_at ? $certificate->generated_at->format('M d, Y') : now()->format('M d, Y'),
                'certificate_id' => 'CERT-' . strtoupper(substr(md5($certificate->id), 0, 8)),
                'final_score' => round($averageScore),
                'instructor_name' => $instructorName,
                'lessons_completed' => $lessonsCompleted,
                'quizzes_passed' => $quizzesPassed
            ]
        ]);
    }

    public function download($id)
    {
        $certificate = Certificate::with(['course', 'user'])->findOrFail($id);
        
        // For now, we'll use the browser's print functionality
        // In production, you would use a PDF library like DomPDF or Snappy
        return Inertia::render('Certificates/Download', [
            'certificate' => [
                'id' => $certificate->id,
                'student_name' => $certificate->user->name ?? auth()->user()->name ?? 'Student Name',
                'course_title' => $certificate->course->title ?? 'Course',
                'issued_date' => $certificate->generated_at ? $certificate->generated_at->format('M d, Y') : now()->format('M d, Y'),
                'certificate_id' => 'CERT-' . strtoupper(substr(md5($certificate->id), 0, 8)),
                'instructor_name' => $certificate->course->creator->name ?? 'Instructor'
            ]
        ]);
    }
}
