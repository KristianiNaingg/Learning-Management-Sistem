<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomAssignSubmission extends Model
{
    use HasFactory;
      protected $table = 'lom_assign_submissions';
      protected $fillable = [
        'assign_id',
        'user_id',
        'file_path',
        'status',
        'submitted_at',
        'created_at',
    ];

    protected $dates = [
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'file_path' => 'array',
        'submitted_at' => 'datetime',
    ];

     public function getFilesAttribute()
    {
        if (is_array($this->file_path)) {
            return collect($this->file_path);
        }

        try {
            return collect(json_decode($this->file_path, true));
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function assignment()
    {
        return $this->belongsTo(LomAssign::class, 'assign_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function grade()
    {
        return $this->hasOne(LomAssignGrade::class, 'submission_id', 'id');
    }

    public function feedback_comments()
    {
        return $this->hasMany(LomFeedbackComment::class, 'assign_id', 'id');
    }

}
