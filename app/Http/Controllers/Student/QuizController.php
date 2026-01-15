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
        $quiz = Quiz::with(['questions.answers', 'course', 'lesson.course'])->findOrFail($id);
        
        // Get the course (either directly or through lesson)
        $course = $quiz->course ?? $quiz->lesson->course;
        
        // Check if student is enrolled
        $isEnrolled = $course->enrollments()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'You must be enrolled in this course to take quizzes.');
        }

        // Get all previous attempts
        $previousAttempts = QuizAttempt::where('quiz_id', $id)
            ->where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->orderBy('id', 'desc')
            ->get();

        $previousAttempt = $previousAttempts->first();
        $attemptCount = $previousAttempts->count();

        // Check if retakes are allowed
        $canRetake = true;
        $retakeMessage = null;

        if ($previousAttempt) {
            if (!$quiz->allow_retake) {
                $canRetake = false;
                $retakeMessage = 'Retakes are not allowed for this quiz.';
            } elseif ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
                $canRetake = false;
                $retakeMessage = "You have reached the maximum number of attempts ({$quiz->max_attempts}).";
            }
        }

        return Inertia::render('Student/Quizzes/Show', [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'time_limit' => $quiz->time_limit,
                'passing_score' => $quiz->passing_score,
                'allow_retake' => $quiz->allow_retake,
                'max_attempts' => $quiz->max_attempts,
                'course_id' => $course->id,
                'questions_count' => $quiz->questions->count()
            ],
            'previousAttempt' => $previousAttempt ? [
                'id' => $previousAttempt->id,
                'score' => $previousAttempt->score,
                'percentage' => $previousAttempt->percentage,
                'passed' => $previousAttempt->percentage >= $quiz->passing_score,
                'submitted_at' => $previousAttempt->completed_at
            ] : null,
            'attemptCount' => $attemptCount,
            'canRetake' => $canRetake,
            'retakeMessage' => $retakeMessage
        ]);
    }

    public function start($id)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        // Check if quiz has questions
        if ($quiz->questions->count() === 0) {
            return redirect()->route('student.quizzes.show', $id)
                ->with('error', 'This quiz has no questions yet. Please contact your instructor.');
        }

        // Get attempt count
        $attemptCount = QuizAttempt::where('quiz_id', $id)
            ->where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->count();

        // Check if retakes are allowed
        if ($attemptCount > 0) {
            if (!$quiz->allow_retake) {
                return redirect()->route('student.quizzes.show', $id)
                    ->with('error', 'Retakes are not allowed for this quiz.');
            }
            
            if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
                return redirect()->route('student.quizzes.show', $id)
                    ->with('error', "You have reached the maximum number of attempts ({$quiz->max_attempts}).");
            }
        }

        // Calculate total points
        $totalPoints = $quiz->questions->sum('points');
        
        // Get attempt number
        $attemptNumber = QuizAttempt::where('quiz_id', $id)
            ->where('user_id', auth()->id())
            ->count() + 1;

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $id,
            'user_id' => auth()->id(),
            'total_points' => $totalPoints,
            'attempt_number' => $attemptNumber
        ]);

        return Inertia::render('Student/Quizzes/Take', [
            'attempt' => [
                'id' => $attempt->id,
                'quiz_id' => $quiz->id,
                'started_at' => $attempt->attempt_date
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

        if ($attempt->completed_at) {
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
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $answerData['question_id'],
                'answer_id' => $answerData['answer_id'] ?? null,
                'short_answer_text' => $answerData['text_answer'] ?? null
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

        $attempt->update([
            'completed_at' => now(),
            'score' => $totalScore,
            'percentage' => $scorePercentage
        ]);

        return redirect()->route('student.quizzes.results', $attempt->id)
            ->with('success', 'Quiz submitted successfully!');
    }

    public function results($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions.answers', 'studentAnswers'])
            ->where('user_id', auth()->id())
            ->findOrFail($attemptId);

        $passed = $attempt->percentage >= $attempt->quiz->passing_score;

        return Inertia::render('Student/Quizzes/Results', [
            'attempt' => [
                'id' => $attempt->id,
                'quiz_id' => $attempt->quiz_id,
                'score' => $attempt->score,
                'percentage' => $attempt->percentage,
                'passed' => $passed,
                'submitted_at' => $attempt->completed_at
            ],
            'quiz' => [
                'title' => $attempt->quiz->title,
                'passing_score' => $attempt->quiz->passing_score,
                'total_points' => $attempt->total_points
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
                    'your_answer' => $selectedAnswer ? $selectedAnswer->answer_text : ($studentAnswer->short_answer_text ?? 'Not answered'),
                    'correct_answer' => $correctAnswer ? $correctAnswer->answer_text : null,
                    'is_correct' => $selectedAnswer && $selectedAnswer->is_correct
                ];
            })
        ]);
    }
}
