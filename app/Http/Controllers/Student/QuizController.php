<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function show($id)
    {
        $quiz = Quiz::with(['questions.answers', 'lesson.course'])->findOrFail($id);
        
        // Check if student is enrolled
        $isEnrolled = $quiz->lesson->course->enrollments()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.courses.show', $quiz->lesson->course_id)
                ->with('error', 'You must be enrolled in this course to take quizzes.');
        }

        $previousAttempt = QuizAttempt::where('quiz_id', $id)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        return Inertia::render('Student/Quizzes/Show', [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'time_limit' => $quiz->time_limit,
                'passing_score' => $quiz->passing_score,
                'course_title' => $quiz->lesson->course->title,
                'lesson_title' => $quiz->lesson->title,
                'questions_count' => $quiz->questions->count()
            ],
            'previousAttempt' => $previousAttempt ? [
                'score' => $previousAttempt->score,
                'passed' => $previousAttempt->passed,
                'submitted_at' => $previousAttempt->submitted_at->format('M d, Y H:i')
            ] : null
        ]);
    }

    public function start($id)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $id,
            'user_id' => auth()->id(),
            'started_at' => now()
        ]);

        return Inertia::render('Student/Quizzes/Take', [
            'attempt' => [
                'id' => $attempt->id,
                'quiz_id' => $quiz->id,
                'started_at' => $attempt->started_at->toIso8601String()
            ],
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'time_limit' => $quiz->time_limit,
                'passing_score' => $quiz->passing_score
            ],
            'questions' => $quiz->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'points' => $question->points,
                    'answers' => $question->answers->map(function($answer) {
                        return [
                            'id' => $answer->id,
                            'answer_text' => $answer->answer_text
                        ];
                    })
                ];
            })
        ]);
    }

    public function submit(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::with('quiz.questions.answers')
            ->where('user_id', auth()->id())
            ->findOrFail($attemptId);

        if ($attempt->submitted_at) {
            return back()->with('error', 'This quiz has already been submitted.');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'nullable|exists:answers,id',
            'answers.*.text_answer' => 'nullable|string'
        ]);

        $totalScore = 0;
        $maxScore = 0;

        foreach ($validated['answers'] as $answerData) {
            $question = $attempt->quiz->questions->find($answerData['question_id']);
            $maxScore += $question->points;

            $studentAnswer = StudentAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $answerData['question_id'],
                'answer_id' => $answerData['answer_id'] ?? null,
                'text_answer' => $answerData['text_answer'] ?? null
            ]);

            // Auto-grade multiple choice and true/false
            if (isset($answerData['answer_id'])) {
                $answer = $question->answers->find($answerData['answer_id']);
                if ($answer && $answer->is_correct) {
                    $totalScore += $question->points;
                }
            }
        }

        $scorePercentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        $passed = $scorePercentage >= $attempt->quiz->passing_score;

        $attempt->update([
            'submitted_at' => now(),
            'score' => $scorePercentage,
            'passed' => $passed,
            'time_taken' => now()->diffInMinutes($attempt->started_at)
        ]);

        return redirect()->route('student.quizzes.results', $attempt->id)
            ->with('success', 'Quiz submitted successfully!');
    }

    public function results($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions.answers', 'studentAnswers'])
            ->where('user_id', auth()->id())
            ->findOrFail($attemptId);

        return Inertia::render('Student/Quizzes/Results', [
            'attempt' => [
                'id' => $attempt->id,
                'score' => $attempt->score,
                'passed' => $attempt->passed,
                'submitted_at' => $attempt->submitted_at->format('M d, Y H:i'),
                'time_taken' => $attempt->time_taken
            ],
            'quiz' => [
                'title' => $attempt->quiz->title,
                'passing_score' => $attempt->quiz->passing_score
            ],
            'questions' => $attempt->quiz->questions->map(function($question) use ($attempt) {
                $studentAnswer = $attempt->studentAnswers->where('question_id', $question->id)->first();
                $selectedAnswer = $studentAnswer && $studentAnswer->answer_id 
                    ? $question->answers->find($studentAnswer->answer_id) 
                    : null;
                $correctAnswer = $question->answers->where('is_correct', true)->first();

                return [
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'points' => $question->points,
                    'your_answer' => $selectedAnswer ? $selectedAnswer->answer_text : ($studentAnswer->text_answer ?? 'Not answered'),
                    'correct_answer' => $correctAnswer ? $correctAnswer->answer_text : null,
                    'is_correct' => $selectedAnswer && $selectedAnswer->is_correct
                ];
            })
        ]);
    }
}
