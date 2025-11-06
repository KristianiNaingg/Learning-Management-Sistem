<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomFile extends Model
{
    use HasFactory;
    protected $table = 'lom_files';
    protected $fillable = [
        'name',
        'description',
        'file_path',
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

    public function folders()

    {
       return $this->belongsToMany(LomFolder::class, 'lom_file_saves', 'file_id', 'folder_id')
                    ->withPivot('id')
                    ->withTimestamps();
    }


}
