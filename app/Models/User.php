<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $fillable = [
        'id',
        'name',
        'email',
        'status',
        'password',
        'id_role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
     protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

     public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Mendefinisikan relasi "belongsToMany" ke model Course melalui tabel pivot course_users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

     public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_users', 'user_id','course_id' )
            ->withPivot('id', 'course_id', 'user_id', 'participant_role')
            ->withTimestamps();
    }

    /**
     * Mendefinisikan relasi "belongsToMany" ke model LearningStyleOption melalui tabel pivot user_learning_style_options.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function learning_style_options()
    {
        return $this->belongsToMany(LearningStyleOption::class, 'user_learning_style_options', 'user_id', 'learning_style_options_id')
            ->withPivot(['dimension', 'a_count', 'b_count', 'final_score', 'category', 'description', 'created_at'])
            ->withTimestamps();
    }

}




