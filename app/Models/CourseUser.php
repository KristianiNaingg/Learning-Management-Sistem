<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    use HasFactory;
    protected $table = 'course_users';

       protected $fillable = [
           'course_id',
           'user_id',
           'participant_role',
          
       ];

    const PARTICIPANT_ROLES = ['Student', 'Teacher', 'Admin'];

       /**
        * Relasi belongs-to dengan Course
        * Menunjukkan course yang terkait dengan entri pivot ini
        */
       public function course()
       {
           return $this->belongsTo(Course::class);
       }

       /**
        * Relasi belongs-to dengan User
        * Menunjukkan user yang terkait dengan entri pivot ini
        */
       public function user()
       {
           return $this->belongsTo(User::class);
       }
}
