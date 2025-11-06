<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomAssign extends Model
{
    use HasFactory;
    protected $table = 'lom_assigns';
    protected $fillable = [
        'subtopic_id',
        'name',
        'description',
        'content',
        'due_date',
        'learning_style_option_id',

    ];
    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class, 'subtopic_id', 'id');
    }

    public function learningStyleOption()
    {
    return $this->belongsTo(LearningStyleOption::class, 'learning_style_option_id','id');
    }

     public function submissions()
    {
        return $this->hasMany(LomAssignSubmission::class, 'assign_id', 'id');
    }

    public function grades()
    {
        return $this->hasMany(LomAssignGrade::class, 'assign_id', 'id');
    }

     public function feedback_comments()
    {
        return $this->hasManyThrough(
            LomFeedbackComment::class,
            LomAssignSubmission::class,
            'assign_id', // FK pada submissions
            'assign_id', // FK pada comments
            'id', // PK assignments
            'id' // PK submissions
        );
    }

}
