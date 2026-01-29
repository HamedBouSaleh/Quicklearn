<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GeminiQuizGeneratorService;
use App\Models\Lesson;

echo "🎓 Testing PDF Quiz Generation\n";
echo "================================\n\n";

// Find lessons with PDF attachments
echo "1. Finding lessons with PDF attachments...\n";

$lessons = Lesson::whereHas('attachments', function($query) {
    $query->where('file_type', 'like', '%pdf%');
})->with('attachments')->get();

if ($lessons->isEmpty()) {
    echo "   ❌ No lessons found with PDF attachments.\n";
    echo "\n   Alternative: Testing with any lesson that has content...\n";
    $lesson = Lesson::whereNotNull('content')->first();
    
    if (!$lesson) {
        echo "   ❌ No lessons found with any content.\n";
        exit(1);
    }
} else {
    $lesson = $lessons->first();
    echo "   ✓ Found lesson: {$lesson->title}\n";
    echo "   PDF attachments:\n";
    foreach ($lesson->attachments as $att) {
        echo "     - {$att->file_name} ({$att->file_type})\n";
    }
}

echo "\n2. Extracting content from lesson...\n";
try {
    $service = new GeminiQuizGeneratorService();
    $content = $service->extractLessonContent($lesson);
    
    echo "   ✓ Content extracted: " . strlen($content) . " characters\n";
    echo "\n   Preview:\n";
    echo "   " . str_repeat("-", 60) . "\n";
    echo "   " . substr($content, 0, 300) . "...\n";
    echo "   " . str_repeat("-", 60) . "\n";
    
    echo "\n3. Generating quiz with AI...\n";
    $questions = $service->generateQuiz(
        $content,
        $lesson->title,
        5,
        ['mcq', 'true_false']
    );
    
    echo "   ✓ Generated " . count($questions) . " questions\n\n";
    
    echo "Sample Questions:\n";
    echo "==================\n";
    foreach ($questions as $i => $q) {
        echo "\nQ" . ($i + 1) . ": {$q['question_text']}\n";
        echo "Type: {$q['question_type']}\n";
        echo "Answers:\n";
        foreach ($q['answers'] as $a) {
            $marker = $a['is_correct'] ? '✓' : ' ';
            echo "  [{$marker}] {$a['answer_text']}\n";
        }
    }
    
    echo "\n✅ SUCCESS! Quiz can be generated from PDF content!\n";
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'pdftotext') !== false) {
        echo "   💡 TIP: For better PDF text extraction, install pdftotext:\n";
        echo "      macOS: brew install poppler\n";
        echo "      Linux: sudo apt-get install poppler-utils\n\n";
    }
    
    exit(1);
}
