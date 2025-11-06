<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomFeedbackComment extends Model
{
    use HasFactory;
    protected $table = 'lom_assignfeedback_comments';
    protected $primaryKey = 'id';

    // Timestamps dinonaktifkan
    public $timestamps = false;

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'submission_id',
        'user_id',
        'comment',
        'created_at',
    ];

    public function submission()
    {
        return $this->belongsTo(LomAssignSubmission::class, 'submission_id', 'id');
    }

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
     public function assignment()
    {
        return $this->hasOneThrough(
            LomAssign::class,
            LomAssignSubmission::class,
            'id', // PK pada submissions
            'id', // PK pada assignments
            'submission_id', // FK comments
            'assign_id' // FK submissions
        );
    }

}
