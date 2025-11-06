<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomQuizAnswer extends Model
{
    use HasFactory;

    protected $table = 'lom_quiz_answers';
    protected $primaryKey = 'id';


    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'poin',
        'user_id',
    ];


    public function attempt()
    {
        return $this->belongsTo(LomQuizAttempt::class, 'attempt_id', 'id');
    }


    public function question()
    {
        return $this->belongsTo(LomQuizQuestion::class, 'question_id', 'id');
    }
}
