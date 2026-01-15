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
        
        // Get instructor name from course creator
        $instructorName = $certificate->course->creator->name ?? 'Instructor';
        
        return Inertia::render('Certificates/Show', [
            'certificate' => [
                'id' => $certificate->id,
                'student_name' => $certificate->user->name ?? auth()->user()->name ?? 'Student Name',
                'course_title' => $certificate->course->title ?? 'Course',
                'issued_date' => $certificate->generated_at ? $certificate->generated_at->format('M d, Y') : now()->format('M d, Y'),
                'certificate_id' => 'CERT-' . strtoupper(substr(md5($certificate->id), 0, 8)),
                'final_score' => 92,
                'instructor_name' => $instructorName,
                'lessons_completed' => $certificate->course->lessons->count() ?? 0,
                'quizzes_passed' => 5,
                'hours_spent' => 12
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
