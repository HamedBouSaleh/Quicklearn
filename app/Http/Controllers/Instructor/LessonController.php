<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function create($courseId)
    {
        $course = Course::where('created_by', auth()->id())->findOrFail($courseId);

        return Inertia::render('Instructor/Lessons/Create', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title
            ]
        ]);
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::where('created_by', auth()->id())->findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'lesson_type' => 'required|in:Video,Article,Mixed',
            'estimated_duration' => 'nullable|integer|min:1',
            'order_position' => 'nullable|integer',
            'attachments.*' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,mp4,zip|max:51200',
            'has_quiz' => 'boolean'
        ]);

        // Create lesson
        $lesson = $course->lessons()->create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'lesson_type' => $validated['lesson_type'],
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'order_position' => $validated['order_position'] ?? $course->lessons()->count() + 1,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('lesson-attachments', 'public');
                $lesson->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_url' => asset('storage/' . $path),
                    'file_type' => strtoupper($file->getClientOriginalExtension()) === 'PDF' ? 'PDF' : (in_array(strtoupper($file->getClientOriginalExtension()), ['JPG', 'JPEG', 'PNG', 'GIF']) ? 'Image' : 'Document'),
                    'file_size_kb' => round($file->getSize() / 1024)
                ]);
            }
        }

        // Create quiz if requested
        if ($request->boolean('has_quiz')) {
            $lesson->quizzes()->create([
                'title' => $validated['title'] . ' Quiz',
                'description' => 'Quiz for ' . $validated['title'],
                'time_limit' => 600, // 10 minutes default
                'passing_score' => 70,
                'max_attempts' => 3
            ]);
        }

        return redirect()->route('instructor.courses.show', $courseId)
            ->with('success', 'Lesson created successfully!' . ($request->boolean('has_quiz') ? ' Quiz added - you can now add questions.' : ''));
    }

    public function edit($id)
    {
        $lesson = Lesson::whereHas('course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($id);

        return Inertia::render('Instructor/Lessons/Edit', [
            'lesson' => [
                'id' => $lesson->id,
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'content' => $lesson->content,
                'type' => $lesson->type,
                'video_url' => $lesson->video_url,
                'duration' => $lesson->duration,
                'order_index' => $lesson->order_index
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::whereHas('course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'type' => 'required|in:video,text,document',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|string',
            'order_index' => 'nullable|integer'
        ]);

        $lesson->update($validated);

        return redirect()->route('instructor.courses.show', $lesson->course_id)
            ->with('success', 'Lesson updated successfully!');
    }

    public function destroy($id)
    {
        $lesson = Lesson::whereHas('course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($id);

        $courseId = $lesson->course_id;
        $lesson->delete();

        return redirect()->route('instructor.courses.show', $courseId)
            ->with('success', 'Lesson deleted successfully!');
    }
}
