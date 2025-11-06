<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomLesson extends Model
{
    use HasFactory;
     protected $table = 'lom_lessons';

    // Primary Key
    protected $primaryKey = 'id';

    // Timestamps dinonaktifkan
    public $timestamps = false;

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'name',
        'description',
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
