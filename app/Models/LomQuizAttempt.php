<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomQuizAttempt extends Model
{
    use HasFactory;
     protected $table = 'lom_quiz_attempts';


    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'start_time',
        'end_time',
        'score',
    ];


    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    public function quiz()
    {
        return $this->belongsTo(LomQuiz::class, 'quiz_id', 'id');
    }


    public function answers()
    {
        return $this->hasMany(LomQuizAnswer::class, 'attempt_id', 'id');
    }

    // Relasi ke Grade
    public function grade()
    {
        return $this->hasOne(LomQuizGrade::class, 'attempt_id', 'id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
