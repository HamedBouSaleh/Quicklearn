<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // My Courses - only enrolled courses
        $courses = Enrollment::where('user_id', auth()->id())
            ->with(['course.creator', 'course.lessons', 'course.quizzes'])
            ->get()
            ->map(function($enrollment) {
                $course = $enrollment->course;
                // Calculate progress dynamically
                $totalLessons = $course->lessons->count();
                $completedLessons = 0;
                if ($totalLessons > 0) {
                    $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', 
                        $course->lessons->pluck('id')
                    )->where('user_id', auth()->id())->count();
                }
                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                // Check if certificate exists
                $certificate = \App\Models\Certificate::where('user_id', auth()->id())
                    ->where('course_id', $course->id)
                    ->first();

                // Calculate total course duration from lessons and quizzes
                $lessonsDuration = $course->lessons->sum('estimated_duration') ?? 0;
                $quizzesDuration = $course->quizzes->whereNotNull('time_limit')->sum('time_limit') ?? 0;
                $totalCourseDuration = $lessonsDuration + $quizzesDuration;
                
                // Format duration as "X hours Y min"
                $hours = floor($totalCourseDuration / 60);
                $minutes = $totalCourseDuration % 60;
                $durationText = '';
                if ($hours > 0) {
                    $durationText .= $hours . ' ' . ($hours == 1 ? 'hour' : 'hours');
                }
                if ($minutes > 0) {
                    if ($hours > 0) $durationText .= ' ';
                    $durationText .= $minutes . ' min';
                }
                if ($totalCourseDuration == 0) {
                    $durationText = 'N/A';
                }

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'short_description' => $course->short_description,
                    'long_description' => $course->long_description,
                    'category' => $course->category,
                    'difficulty' => $course->difficulty,
                    'estimated_duration' => $durationText,
                    'thumbnail_url' => $course->thumbnail_url,
                    'enrollment_count' => $course->enrollment_count ?? 0,
                    'instructor_name' => $course->creator->name ?? 'Instructor',
                    'is_enrolled' => true,
                    'progress' => $progress,
                    'enrolled_at' => $enrollment->created_at,
                    'certificate_id' => $certificate ? $certificate->id : null
                ];
            });

        return Inertia::render('Student/Courses/MyCourses', [
            'courses' => $courses
        ]);
    }

    public function browse(Request $request)
    {
        $query = Course::query()
            ->with('instructor')
            ->withCount('enrollments')
            ->where('is_published', true); // Only show published courses

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('long_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Difficulty filter
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));
        }

        // Sort
        switch ($request->input('sort', 'newest')) {
            case 'popular':
                $query->orderByDesc('enrollment_count');
                break;
            case 'title':
                $query->orderBy('title');
                break;
            default: // newest
                $query->latest();
        }

        $courses = $query->get()->map(function($course) {
            $enrollment = Enrollment::where('course_id', $course->id)
                ->where('user_id', auth()->id())
                ->first();

            // Calculate progress dynamically
            $progress = 0;
            if ($enrollment) {
                $totalLessons = \App\Models\Lesson::where('course_id', $course->id)->count();
                if ($totalLessons > 0) {
                    $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', function($query) use ($course) {
                        $query->select('id')->from('lessons')->where('course_id', $course->id);
                    })->where('user_id', auth()->id())->count();
                    $progress = round(($completedLessons / $totalLessons) * 100);
                }
            }

            return [
                'id' => $course->id,
                'title' => $course->title,
                'short_description' => $course->short_description,
                'long_description' => $course->long_description,
                'category' => $course->category,
                'difficulty' => $course->difficulty,
                'estimated_duration' => $course->estimated_duration,
                'thumbnail_url' => $course->thumbnail_url,
                'enrollment_count' => $course->enrollment_count,
                'instructor_name' => $course->instructor->name,
                'is_enrolled' => !!$enrollment,
                'progress' => $progress
            ];
        });

        return Inertia::render('Student/Courses/Browse', [
            'courses' => $courses,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'difficulty' => $request->input('difficulty', ''),
                'sort' => $request->input('sort', 'newest')
            ]
        ]);
    }

    public function show($id)
    {
        $course = Course::with(['lessons', 'creator', 'quizzes'])->findOrFail($id);
        $enrollment = Enrollment::where('course_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        // Get actual enrollment count
        $enrollmentCount = Enrollment::where('course_id', $id)->count();

        // Calculate total course duration from lessons and quizzes
        $lessonsDuration = $course->lessons->sum('estimated_duration') ?? 0;
        $quizzesDuration = $course->quizzes->whereNotNull('time_limit')->sum('time_limit') ?? 0;
        $totalCourseDuration = $lessonsDuration + $quizzesDuration;

        // Calculate progress dynamically
        $progress = 0;
        if ($enrollment) {
            $totalLessons = $course->lessons->count();
            if ($totalLessons > 0) {
                $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', 
                    $course->lessons->pluck('id')
                )->where('user_id', auth()->id())->count();
                $progress = round(($completedLessons / $totalLessons) * 100);
            }
        }
        \Log::info("CourseController.show - Course: {$id}, User: " . auth()->id() . ", Progress: {$progress}");

        // Check if certificate exists for this course
        $certificate = null;
        if ($progress == 100) {
            $certificate = \App\Models\Certificate::where('user_id', auth()->id())
                ->where('course_id', $id)
                ->first();
            
            if ($certificate) {
                $certificate = [
                    'id' => $certificate->id,
                    'verification_code' => $certificate->verification_code,
                    'generated_at' => $certificate->generated_at->format('M d, Y')
                ];
            }
        }

        return Inertia::render('Student/Courses/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'short_description' => $course->short_description,
                'long_description' => $course->long_description,
                'category' => $course->category,
                'difficulty' => $course->difficulty,
                'estimated_duration' => $totalCourseDuration,
                'thumbnail_url' => $course->thumbnail_url,
                'enrollment_count' => $enrollmentCount,
                'instructor_name' => $course->creator->name ?? 'Instructor',
                'instructor_email' => $course->creator->email ?? '',
                'instructor_bio' => $course->instructor_bio,
                'is_enrolled' => !!$enrollment,
                'progress' => $progress
            ],
            'certificate' => $certificate,
            'lessons' => $course->lessons->map(function($lesson) use ($enrollment) {
                $isCompleted = false;
                if ($enrollment) {
                    $isCompleted = \App\Models\LessonCompletion::where('lesson_id', $lesson->id)
                        ->where('user_id', auth()->id())
                        ->exists();
                }

                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'content' => $lesson->content,
                    'lesson_type' => $lesson->lesson_type,
                    'estimated_duration' => $lesson->estimated_duration,
                    'is_completed' => $isCompleted
                ];
            }),
            'quizzes' => $course->quizzes->map(function($quiz) use ($enrollment) {
                $bestAttempt = null;
                $canRetake = true;
                
                if ($enrollment) {
                    // Get the best attempt for this quiz
                    $attempts = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                        ->where('user_id', auth()->id())
                        ->whereNotNull('completed_at')
                        ->get();
                    
                    if ($attempts->count() > 0) {
                        $best = $attempts->sortByDesc('percentage')->first();
                        $bestAttempt = [
                            'id' => $best->id,
                            'percentage' => $best->percentage,
                            'passed' => $best->percentage >= $quiz->passing_score
                        ];
                        
                        // Check if can retake
                        if (!$quiz->allow_retake) {
                            $canRetake = false;
                        } elseif ($quiz->max_attempts && $attempts->count() >= $quiz->max_attempts) {
                            $canRetake = false;
                        }
                    }
                }
                
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'passing_score' => $quiz->passing_score,
                    'time_limit' => $quiz->time_limit,
                    'lesson_id' => $quiz->lesson_id,
                    'best_attempt' => $bestAttempt,
                    'can_retake' => $canRetake,
                    'allow_retake' => $quiz->allow_retake,
                    'max_attempts' => $quiz->max_attempts
                ];
            })
        ]);
    }

    public function enroll($id)
    {
        $course = Course::findOrFail($id);
        
        $existing = Enrollment::where('course_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return back()->with('info', 'You are already enrolled in this course.');
        }

        Enrollment::create([
            'course_id' => $id,
            'user_id' => auth()->id(),
            'enrolled_at' => now()
        ]);

        return redirect()->route('student.courses.show', $id)
            ->with('success', 'Successfully enrolled in the course!');
    }
}
