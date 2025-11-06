<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'full_name', 'short_name', 'summary', 'cpmk', 'course_image',
        'semester', 'visible', 'category', 'start_date', 'end_date',
    ];

    protected $dates = ['start_date', 'end_date'];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class, 'course_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_users','course_id','user_id' )
                    ->withPivot('id', 'course_id', 'user_id', 'participant_role')
                    ->withTimestamps();
    }

}
