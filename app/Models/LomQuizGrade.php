<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomQuizGrade extends Model
{
    use HasFactory;
    protected $table = 'lom_quiz_grades';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_id',
        'grade',
        'attempt_number',
        'completed_at',
    ];


    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(LomQuiz::class, 'quiz_id', 'id');
    }

    public function attempt()
    {
        return $this->belongsTo(LomQuizAttempt::class, 'attempt_id', 'id');
    }
}
