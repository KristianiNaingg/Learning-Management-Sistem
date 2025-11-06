<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomAssignGrade extends Model
{
    use HasFactory;
    protected $table = 'lom_assign_grades';

    protected $fillable = [
        'assign_id',
        'user_id',
        'submission_id',
        'grade',
        'feedback',
        
    ];

     public function assignment()
    {
        return $this->belongsTo(LomAssign::class, 'assign_id', 'id');
    }

    public function submission()
    {
        return $this->belongsTo(LomAssignSubmission::class, 'submission_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
