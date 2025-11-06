<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomPage extends Model
{
    use HasFactory;
    protected $table = 'lom_pages';

    protected $fillable = [
        'name',
        'description',
        'content',
        'subtopic_id',
        'learning_style_option_id',

    ];
    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class, 'subtopic_id', 'id');
    }

     public function learningStyleOption()
    {
    return $this->belongsTo(LearningStyleOption::class, 'learning_style_option_id','id');
    }
}
