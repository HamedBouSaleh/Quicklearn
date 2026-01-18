<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function index($lessonId)
    {
        $lesson = Lesson::with(['course', 'quizzes.questions', 'quizzes.attempts'])
            ->whereHas('course', function($query) {
                $query->where('created_by', auth()->id());
            })->findOrFail($lessonId);

        return Inertia::render('Instructor/Quizzes/Index', [
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'course_id' => $lesson->course_id,
                'course_title' => $lesson->course->title
            ],
            'quizzes' => $lesson->quizzes->map(function($quiz) {
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'time_limit' => $quiz->time_limit,
                    'passing_score' => $quiz->passing_score,
                    'questions_count' => $quiz->questions->count(),
                    'attempts_count' => $quiz->attempts->count(),
                    'created_at' => $quiz->created_at ? $quiz->created_at->format('M d, Y') : null
                ];
            })
        ]);
    }

    public function create($lessonId)
    {
        $lesson = Lesson::whereHas('course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($lessonId);

        return Inertia::render('Instructor/Quizzes/Create', [
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'course_id' => $lesson->course_id
            ]
        ]);
    }

    public function store(Request $request, $lessonId)
    {
        $lesson = Lesson::whereHas('course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($lessonId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer',
            'passing_score' => 'required|integer|min:0|max:100',
            'allow_retake' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required|array|min:1',
            'questions.*.answers.*.answer_text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        $quiz = $lesson->quizzes()->create([
            'course_id' => $lesson->course_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'time_limit' => $validated['time_limit'],
            'passing_score' => $validated['passing_score'],
            'allow_retake' => $request->boolean('allow_retake', true),
            'max_attempts' => $validated['max_attempts'] ?? 3
        ]);

        // Map question types from frontend to database enum
        $questionTypeMap = [
            'multiple_choice' => 'MCQ',
            'true_false' => 'TrueFalse',
            'short_answer' => 'ShortAnswer'
        ];

        foreach ($validated['questions'] as $index => $questionData) {
            $question = $quiz->questions()->create([
                'question_text' => $questionData['question_text'],
                'question_type' => $questionTypeMap[$questionData['question_type']] ?? 'MCQ',
                'points' => $questionData['points'],
                'order_position' => $index + 1
            ]);

            foreach ($questionData['answers'] as $answerIndex => $answerData) {
                $question->answers()->create([
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct'],
                    'order_position' => $answerIndex + 1
                ]);
            }
        }

        return redirect()->route('instructor.courses.show', $lesson->course_id)
            ->with('success', 'Quiz created successfully!');
    }

    public function edit($id)
    {
        $quiz = Quiz::with(['questions.answers', 'lesson.course'])
            ->whereHas('lesson.course', function($query) {
                $query->where('created_by', auth()->id());
            })->findOrFail($id);

        return Inertia::render('Instructor/Quizzes/Edit', [
            'quiz' => [
                'id' => $quiz->id,
                'lesson_id' => $quiz->lesson_id,
                'course_id' => $quiz->lesson->course_id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'time_limit' => $quiz->time_limit,
                'passing_score' => $quiz->passing_score,
                'allow_retake' => $quiz->allow_retake,
                'max_attempts' => $quiz->max_attempts,
                'questions' => $quiz->questions->map(function($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'question_type' => $question->question_type,
                        'points' => $question->points,
                        'answers' => $question->answers->map(function($answer) {
                            return [
                                'id' => $answer->id,
                                'answer_text' => $answer->answer_text,
                                'is_correct' => $answer->is_correct
                            ];
                        })
                    ];
                })
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::whereHas('lesson.course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer',
            'passing_score' => 'required|integer|min:0|max:100',
            'allow_retake' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|integer',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required|array|min:1',
            'questions.*.answers.*.id' => 'nullable|integer',
            'questions.*.answers.*.answer_text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        // Update quiz basic info
        $quiz->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'time_limit' => $validated['time_limit'],
            'passing_score' => $validated['passing_score'],
            'allow_retake' => $request->boolean('allow_retake', true),
            'max_attempts' => $validated['max_attempts'] ?? null
        ]);

        // Map question types from frontend to database enum
        $questionTypeMap = [
            'multiple_choice' => 'MCQ',
            'true_false' => 'TrueFalse',
            'short_answer' => 'ShortAnswer'
        ];

        // Track existing question IDs to know which ones to keep
        $submittedQuestionIds = collect($validated['questions'])
            ->pluck('id')
            ->filter()
            ->toArray();

        // Delete questions that were removed
        $quiz->questions()->whereNotIn('id', $submittedQuestionIds)->delete();

        // Update or create questions
        foreach ($validated['questions'] as $index => $questionData) {
            if (isset($questionData['id'])) {
                // Update existing question
                $question = $quiz->questions()->find($questionData['id']);
                if ($question) {
                    $question->update([
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionTypeMap[$questionData['question_type']] ?? 'MCQ',
                        'points' => $questionData['points'],
                        'order_position' => $index + 1
                    ]);

                    // Track existing answer IDs
                    $submittedAnswerIds = collect($questionData['answers'])
                        ->pluck('id')
                        ->filter()
                        ->toArray();

                    // Delete answers that were removed
                    $question->answers()->whereNotIn('id', $submittedAnswerIds)->delete();

                    // Update or create answers
                    foreach ($questionData['answers'] as $answerIndex => $answerData) {
                        if (isset($answerData['id'])) {
                            // Update existing answer
                            $answer = $question->answers()->find($answerData['id']);
                            if ($answer) {
                                $answer->update([
                                    'answer_text' => $answerData['answer_text'],
                                    'is_correct' => $answerData['is_correct'],
                                    'order_position' => $answerIndex + 1
                                ]);
                            }
                        } else {
                            // Create new answer
                            $question->answers()->create([
                                'answer_text' => $answerData['answer_text'],
                                'is_correct' => $answerData['is_correct'],
                                'order_position' => $answerIndex + 1
                            ]);
                        }
                    }
                }
            } else {
                // Create new question
                $question = $quiz->questions()->create([
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionTypeMap[$questionData['question_type']] ?? 'MCQ',
                    'points' => $questionData['points'],
                    'order_position' => $index + 1
                ]);

                // Create answers for new question
                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    $question->answers()->create([
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'],
                        'order_position' => $answerIndex + 1
                    ]);
                }
            }
        }

        return redirect()->route('instructor.quizzes.show', $id)
            ->with('success', 'Quiz updated successfully!');
    }

    public function show($id)
    {
        $quiz = Quiz::with(['questions.answers', 'lesson.course', 'attempts.user'])
            ->whereHas('lesson.course', function($query) {
                $query->where('created_by', auth()->id());
            })->findOrFail($id);

        return Inertia::render('Instructor/Quizzes/Show', [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'time_limit' => $quiz->time_limit,
                'passing_score' => $quiz->passing_score,
                'course_title' => $quiz->lesson->course->title,
                'lesson_title' => $quiz->lesson->title,
                'questions_count' => $quiz->questions->count(),
                'attempts_count' => $quiz->attempts->count()
            ],
            'attempts' => $quiz->attempts->map(function($attempt) use ($quiz) {
                return [
                    'id' => $attempt->id,
                    'student_name' => $attempt->user->name,
                    'student_email' => $attempt->user->email,
                    'score' => round($attempt->percentage, 1),
                    'passed' => $attempt->percentage >= $quiz->passing_score,
                    'submitted_at' => $attempt->completed_at ? $attempt->completed_at->format('M d, Y H:i') : null,
                    'time_taken' => $attempt->time_spent_seconds ? round($attempt->time_spent_seconds / 60) : null
                ];
            })
        ]);
    }

    public function destroy($id)
    {
        $quiz = Quiz::whereHas('lesson.course', function($query) {
            $query->where('created_by', auth()->id());
        })->findOrFail($id);

        $courseId = $quiz->lesson->course_id;
        $quiz->delete();

        return redirect()->route('instructor.courses.show', $courseId)
            ->with('success', 'Quiz deleted successfully!');
    }

    public function gradeAttempt($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions.answers', 'studentAnswers', 'user'])
            ->whereHas('quiz.lesson.course', function($query) {
                $query->where('created_by', auth()->id());
            })->findOrFail($attemptId);

        return Inertia::render('Instructor/Quizzes/GradeAttempt', [
            'attempt' => [
                'id' => $attempt->id,
                'student_name' => $attempt->user->name,
                'quiz_id' => $attempt->quiz_id,
                'quiz_title' => $attempt->quiz->title,
                'score' => round($attempt->percentage, 1),
                'passed' => $attempt->percentage >= $attempt->quiz->passing_score,
                'submitted_at' => $attempt->completed_at ? $attempt->completed_at->format('M d, Y H:i') : null
            ],
            'questions' => $attempt->quiz->questions->map(function($question) use ($attempt) {
                $studentAnswer = $attempt->studentAnswers->where('question_id', $question->id)->first();
                $selectedAnswerId = $studentAnswer ? $studentAnswer->answer_id : null;
                $textAnswer = $studentAnswer ? $studentAnswer->text_answer : null;
                
                return [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'points' => $question->points,
                    'student_answer_id' => $selectedAnswerId,
                    'student_text_answer' => $textAnswer,
                    'answers' => $question->answers->map(function($answer) use ($selectedAnswerId) {
                        return [
                            'id' => $answer->id,
                            'answer_text' => $answer->answer_text,
                            'is_correct' => $answer->is_correct,
                            'is_selected' => $answer->id === $selectedAnswerId
                        ];
                    })
                ];
            })
        ]);
    }
}
