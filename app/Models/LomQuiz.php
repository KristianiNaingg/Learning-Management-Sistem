<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomQuiz extends Model
{
    use HasFactory;

     protected $table = 'lom_quizzes';
    protected $primaryKey = 'id';

    protected $fillable = [

        'subtopic_id',
        'name',
        'description',
        'time_open',
        'time_close',
        'time_limit',
        'max_attempts',
        'grade_to_pass',
        'learning_dimension_id',

    ];


    protected $casts = [
        'time_open' => 'datetime',
        'time_close' => 'datetime',
    ];


    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class, 'subtopic_id', 'id');
    }


    public function attempts()
    {
        return $this->hasMany(LomQuizAttempt::class, 'quiz_id', 'id');
    }


    public function grades()
    {
        return $this->hasMany(LomQuizGrade::class, 'quiz_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(LomQuizQuestion::class, 'quiz_id', 'id');
    }

    // Relasi ke Learning Dimensions
    public function learningDimension()
    {
    return $this->belongsTo(LearningDimension::class, 'learning_dimension_id','id');
    }



}
