<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomFolder extends Model
{
    use HasFactory;
    protected $table = 'lom_folders';
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


public function files()

    {
       return $this->belongsToMany(LomFile::class, 'lom_file_saves', 'file_id', 'folder_id')
                    ->withPivot('id')
                    ->withTimestamps();
    }


}
