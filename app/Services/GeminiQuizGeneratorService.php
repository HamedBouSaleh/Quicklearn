<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiQuizGeneratorService
{
    protected $client;
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Generate quiz questions from lesson content
     *
     * @param string $content The lesson content (article text or video transcript)
     * @param string $lessonTitle The title of the lesson
     * @param int $numberOfQuestions Number of questions to generate
     * @param array $questionTypes Types of questions to generate ['mcq', 'true_false']
     * @return array Generated questions
     */
    public function generateQuiz($content, $lessonTitle, $numberOfQuestions = 5, $questionTypes = ['mcq', 'true_false'])
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.');
        }

        // Prepare the prompt
        $prompt = $this->buildPrompt($content, $lessonTitle, $numberOfQuestions, $questionTypes);

        try {
            // Make API request
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ]
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            // Extract the generated text
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Parse the JSON response
            return $this->parseQuizResponse($generatedText);

        } catch (Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            throw new Exception('Failed to generate quiz: ' . $e->getMessage());
        }
    }

    /**
     * Build the prompt for Gemini
     */
    protected function buildPrompt($content, $lessonTitle, $numberOfQuestions, $questionTypes)
    {
        $questionTypesStr = implode(' and ', array_map(function($type) {
            return $type === 'mcq' ? 'multiple choice (4 options)' : 'true/false';
        }, $questionTypes));

        $prompt = <<<PROMPT
You are an expert educational content creator. Based on the following lesson content, generate {$numberOfQuestions} quiz questions.

Lesson Title: {$lessonTitle}

Lesson Content:
{$content}

Generate {$numberOfQuestions} questions using {$questionTypesStr} formats. Mix the question types evenly.

IMPORTANT: Respond ONLY with valid JSON in the following format (no markdown, no code blocks, just raw JSON):

{
  "questions": [
    {
      "question_text": "What is...",
      "question_type": "multiple_choice",
      "points": 10,
      "answers": [
        {"answer_text": "Option A", "is_correct": false},
        {"answer_text": "Option B", "is_correct": true},
        {"answer_text": "Option C", "is_correct": false},
        {"answer_text": "Option D", "is_correct": false}
      ]
    },
    {
      "question_text": "Statement is true?",
      "question_type": "true_false",
      "points": 10,
      "answers": [
        {"answer_text": "True", "is_correct": true},
        {"answer_text": "False", "is_correct": false}
      ]
    }
  ]
}

Guidelines:
1. Questions should test understanding, not just memorization
2. For multiple choice: provide 4 options with only 1 correct answer
3. For true/false: provide exactly 2 options (True and False)
4. Make distractors (wrong answers) plausible but clearly incorrect
5. Ensure questions are clear and unambiguous
6. Each question is worth 10 points
7. Base all questions on the actual content provided

Return ONLY the JSON object, nothing else.
PROMPT;

        return $prompt;
    }

    /**
     * Parse the Gemini response and extract questions
     */
    protected function parseQuizResponse($responseText)
    {
        // Remove markdown code blocks if present
        $responseText = preg_replace('/```json\s*/', '', $responseText);
        $responseText = preg_replace('/```\s*/', '', $responseText);
        $responseText = trim($responseText);

        // Decode JSON
        $data = json_decode($responseText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse quiz response: Invalid JSON format');
        }

        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new Exception('Invalid quiz format: questions array not found');
        }

        // Validate and format questions
        $questions = [];
        foreach ($data['questions'] as $index => $question) {
            // Validate required fields
            if (empty($question['question_text']) || empty($question['question_type']) || empty($question['answers'])) {
                continue; // Skip invalid questions
            }

            // Ensure at least one correct answer
            $hasCorrect = false;
            foreach ($question['answers'] as $answer) {
                if ($answer['is_correct'] ?? false) {
                    $hasCorrect = true;
                    break;
                }
            }

            if (!$hasCorrect) {
                continue; // Skip questions without correct answers
            }

            $questions[] = [
                'question_text' => $question['question_text'],
                'question_type' => $question['question_type'],
                'points' => $question['points'] ?? 10,
                'answers' => $question['answers']
            ];
        }

        return $questions;
    }

    /**
     * Extract content from a lesson for quiz generation
     */
    public function extractLessonContent($lesson)
    {
        $content = '';

        // Add title
        $content .= "Title: " . $lesson->title . "\n\n";

        // Add article content if exists
        if (!empty($lesson->content)) {
            $content .= "Content:\n" . strip_tags($lesson->content) . "\n\n";
        }

        // If it's a video lesson, note that (transcript would need separate service)
        if (!empty($lesson->video_url)) {
            $content .= "Note: This lesson includes a video. ";
            
            // If YouTube video, we could fetch transcript
            if (strpos($lesson->video_url, 'youtube.com') !== false || strpos($lesson->video_url, 'youtu.be') !== false) {
                try {
                    $youtubeService = new YouTubeTranscriptService();
                    $transcript = $youtubeService->getTranscript($lesson->video_url);
                    if ($transcript) {
                        $content .= "\n\nVideo Transcript:\n" . $transcript;
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to fetch YouTube transcript: ' . $e->getMessage());
                    $content .= "Video URL: " . $lesson->video_url;
                }
            }
        }

        // Extract text from PDF attachments
        $attachments = $lesson->attachments()->where('file_type', 'like', '%pdf%')->get();
        if ($attachments->count() > 0) {
            foreach ($attachments as $attachment) {
                try {
                    $pdfContent = $this->extractPdfContent($attachment->file_url);
                    if ($pdfContent) {
                        $content .= "\n\nPDF Content from {$attachment->file_name}:\n" . $pdfContent . "\n";
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to extract PDF content: ' . $e->getMessage());
                }
            }
        }

        if (empty(trim($content))) {
            throw new Exception('No content available to generate quiz from. Please add article content, video, or PDF attachments to the lesson.');
        }

        return $content;
    }

    /**
     * Extract text content from PDF file
     */
    protected function extractPdfContent($fileUrl)
    {
        // Convert relative path to absolute
        if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
            $filePath = storage_path('app/public/' . ltrim($fileUrl, '/'));
        } else {
            // For URLs, we would need to download first
            $filePath = $fileUrl;
        }

        if (!file_exists($filePath)) {
            throw new Exception("PDF file not found: {$filePath}");
        }

        // Use pdftotext if available (common on Linux/Mac)
        if (function_exists('shell_exec')) {
            $output = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
            if (!empty($output)) {
                return $output;
            }
        }

        // Fallback: Basic PDF text extraction using simple parsing
        // This is a very basic approach and won't work for all PDFs
        $content = file_get_contents($filePath);
        
        // Try to extract text between stream objects
        if (preg_match_all('/stream\s*(.*?)\s*endstream/s', $content, $matches)) {
            $text = '';
            foreach ($matches[1] as $match) {
                // Try to decode if it's compressed
                $decoded = @gzuncompress($match);
                if ($decoded !== false) {
                    $text .= $decoded . ' ';
                } else {
                    $text .= $match . ' ';
                }
            }
            
            // Clean up extracted text
            $text = preg_replace('/[^\x20-\x7E\s]/', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            
            if (!empty(trim($text))) {
                return substr($text, 0, 10000); // Limit to 10k chars
            }
        }

        throw new Exception("Could not extract text from PDF. Please add text content to the lesson or install pdftotext command.");
    }
}
