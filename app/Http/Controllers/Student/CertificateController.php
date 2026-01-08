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
            ->latest()
            ->get()
            ->map(function($cert) {
                return [
                    'id' => $cert->id,
                    'course_title' => $cert->course->title ?? 'Course',
                    'completion_date' => $cert->created_at->format('M d, Y')
                ];
            });

        return Inertia::render('Student/Certificates/Index', [
            'certificates' => $certificates
        ]);
    }

    public function show($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        return Inertia::render('Certificates/Show', [
            'certificate' => [
                'id' => $certificate->id,
                'student_name' => auth()->user()->name ?? 'Student Name',
                'course_title' => $certificate->course->title ?? 'Course',
                'issued_date' => $certificate->created_at->format('M d, Y'),
                'certificate_id' => 'CERT-' . strtoupper(substr(md5($certificate->id), 0, 8)),
                'final_score' => 92,
                'instructor_name' => $certificate->course->instructor_name ?? 'Instructor',
                'lessons_completed' => $certificate->course->lessons_count ?? 0,
                'quizzes_passed' => 5,
                'hours_spent' => 12
            ]
        ]);
    }

    public function download($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        // TODO: Implement PDF generation and download
        // For now, just redirect back
        return redirect()->back();
    }
}
