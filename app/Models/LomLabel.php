<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomLabel extends Model
{
    use HasFactory;
    protected $table = 'lom_labels';


    protected $primaryKey = 'id';

    protected $fillable = [
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
