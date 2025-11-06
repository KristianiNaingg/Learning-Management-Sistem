<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomQuizQuestion extends Model
{
    use HasFactory;
    protected $table = 'lom_quiz_questions';

    protected $primaryKey = 'id';

      protected $fillable = [
        'quiz_id',
        'question_text',
        'options_a',
        'options_b',
        'options_c',
        'options_d',
        'correct_answer',
        'poin',
    ];

    public function quiz()
    {
        return $this->belongsTo(LomQuiz::class, 'quiz_id', 'id');
    }
}
