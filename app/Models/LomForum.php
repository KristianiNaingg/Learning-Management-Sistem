<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomForum extends Model
{
    use HasFactory;
        protected $table = 'lom_forums';
        protected $fillable = [
        'subtopic_id',
        'name',
        'description',
        'learning_style_option_id',


    ];
     public function subtopic()
    {
        return $this->belongsTo(Subtopic::class, 'subtopic_id', 'id');
    }
     public function posts()
    {
        return $this->hasMany(LomForumPost::class, 'forum_id', 'id');
    }
   public function learningStyleOption()
    {
    return $this->belongsTo(LearningStyleOption::class, 'learning_style_option_id','id');
    }

}
