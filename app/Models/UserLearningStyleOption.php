<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLearningStyleOption extends Model
{
    use HasFactory;

   protected $table = 'user_learning_style_options';

    protected $fillable = [
        'user_id',
        'learning_style_option_id',
        'dimension',
        'a_count',
        'b_count',
        'final_score',
        'category',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function learningStyleOption()
    {
        return $this->belongsTo(LearningStyleOption::class, 'learning_style_option_id');
    }
}
