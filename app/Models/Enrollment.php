<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'course_id',
        'last_accessed_at',
        'status',
        'progress_percentage'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
        'last_accessed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Accessor to calculate progress dynamically based on current lessons
    public function getProgressPercentageAttribute($value)
    {
        $totalLessons = \App\Models\Lesson::where('course_id', $this->course_id)->count();
        
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', function($query) {
            $query->select('id')
                ->from('lessons')
                ->where('course_id', $this->course_id);
        })->where('user_id', $this->user_id)->count();

        return round(($completedLessons / $totalLessons) * 100);
    }
}
