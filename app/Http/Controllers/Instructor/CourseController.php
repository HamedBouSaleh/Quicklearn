<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('created_by', auth()->id())
            ->withCount(['lessons', 'enrollments'])
            ->latest()
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'short_description' => $course->short_description,
                    'thumbnail_url' => $course->thumbnail_url,
                    'category' => $course->category,
                    'difficulty' => $course->difficulty,
                    'is_published' => $course->is_published,
                    'students_count' => $course->enrollments_count,
                    'lessons_count' => $course->lessons_count,
                    'created_at' => $course->created_at->format('M d, Y')
                ];
            });

        return Inertia::render('Instructor/Courses/Index', [
            'courses' => $courses
        ]);
    }

    public function create()
    {
        return Inertia::render('Instructor/Courses/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'long_description' => 'required|string',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'estimated_duration' => 'nullable|integer|min:1',
            'thumbnail_url' => 'nullable|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_published' => 'boolean',
            'instructor_bio' => 'nullable|string|max:1000'
        ]);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course = Course::create([
            'title' => $validated['title'],
            'short_description' => $validated['short_description'],
            'long_description' => $validated['long_description'],
            'category' => $validated['category'],
            'difficulty' => $validated['difficulty'],
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'thumbnail_url' => $thumbnailPath ? asset('storage/' . $thumbnailPath) : ($validated['thumbnail_url'] ?? null),
            'is_published' => $request->boolean('is_published', false),
            'created_by' => auth()->id(),
            'enrollment_count' => 0,
            'instructor_bio' => $validated['instructor_bio'] ?? null
        ]);

        return redirect()->route('instructor.courses.show', $course->id)
            ->with('success', 'Course created successfully!');
    }

    public function show($id)
    {
        $course = Course::with(['lessons.quizzes', 'lessons.attachments', 'enrollments.user', 'quizzes'])
            ->where('created_by', auth()->id())
            ->findOrFail($id);

        // Calculate total duration from lessons and quizzes
        $lessonsDuration = $course->lessons->sum('estimated_duration') ?? 0;
        $quizzesDuration = $course->quizzes->whereNotNull('time_limit')->sum('time_limit') ?? 0;
        $totalDuration = $lessonsDuration + $quizzesDuration;

        return Inertia::render('Instructor/Courses/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'short_description' => $course->short_description,
                'long_description' => $course->long_description,
                'category' => $course->category,
                'difficulty' => $course->difficulty,
                'estimated_duration' => $totalDuration,
                'thumbnail_url' => $course->thumbnail_url,
                'is_published' => $course->is_published,
                'students_count' => $course->enrollments->count(),
                'lessons_count' => $course->lessons->count(),
                'created_at' => $course->created_at->format('M d, Y')
            ],
            'lessons' => $course->lessons->sortBy('order_position')->values()->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'content' => $lesson->content,
                    'lesson_type' => $lesson->lesson_type,
                    'video_url' => $lesson->video_url,
                    'order_position' => $lesson->order_position,
                    'estimated_duration' => $lesson->estimated_duration,
                    'quizzes_count' => $lesson->quizzes->count(),
                    'attachments_count' => $lesson->attachments->count(),
                ];
            }),
            'students' => $course->enrollments->map(function($enrollment) {
                return [
                    'id' => $enrollment->user->id,
                    'name' => $enrollment->user->name,
                    'email' => $enrollment->user->email,
                    'enrolled_at' => $enrollment->created_at ? $enrollment->created_at->format('M d, Y') : 'N/A',
                    'progress' => $enrollment->progress_percentage ?? 0,
                ];
            })
        ]);
    }

    public function edit($id)
    {
        $course = Course::where('created_by', auth()->id())->findOrFail($id);

        return Inertia::render('Instructor/Courses/Edit', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'short_description' => $course->short_description,
                'long_description' => $course->long_description,
                'category' => $course->category,
                'difficulty' => $course->difficulty,
                'estimated_duration' => $course->estimated_duration,
                'thumbnail_url' => $course->thumbnail_url,
                'is_published' => $course->is_published,
                'instructor_bio' => $course->instructor_bio
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('created_by', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'long_description' => 'required|string',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'estimated_duration' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_published' => 'boolean',
            'instructor_bio' => 'nullable|string|max:1000'
        ]);

        // Handle thumbnail upload if a new file is provided
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $validated['thumbnail_url'] = asset('storage/' . $thumbnailPath);
        }

        // Update course with validated data
        $course->update([
            'title' => $validated['title'],
            'short_description' => $validated['short_description'],
            'long_description' => $validated['long_description'],
            'category' => $validated['category'],
            'difficulty' => $validated['difficulty'],
            'estimated_duration' => $validated['estimated_duration'] ?? $course->estimated_duration,
            'thumbnail_url' => $validated['thumbnail_url'] ?? $course->thumbnail_url,
            'is_published' => $request->boolean('is_published', false),
            'instructor_bio' => $validated['instructor_bio'] ?? $course->instructor_bio
        ]);

        return redirect()->route('instructor.courses.show', $course->id)
            ->with('success', 'Course updated successfully!');
    }

    public function publish($id)
    {
        $course = Course::where('created_by', auth()->id())->findOrFail($id);
        $course->update(['is_published' => request()->boolean('is_published')]);
        
        return back()->with('success', 'Course ' . ($course->is_published ? 'published' : 'unpublished') . ' successfully!');
    }

    public function destroy($id)
    {
        $course = Course::where('created_by', auth()->id())->findOrFail($id);
        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course deleted successfully!');
    }
}
