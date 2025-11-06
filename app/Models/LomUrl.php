<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomUrl extends Model
{
    use HasFactory;

    protected $table = 'lom_urls';



    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'subtopic_id',
        'name',
        'url_link',
         'learning_style_option_id',
        'description',
    ];

    // Relasi ke Subtopic
    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class, 'subtopic_id', 'id');
    }

     public function learningStyleOption()
    {
    return $this->belongsTo(LearningStyleOption::class, 'learning_style_option_id','id');
    }

}
