<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtopic extends Model
{
    use HasFactory;
    protected $table = 'subtopics';

protected $primaryKey = 'id';

public $timestamps = true;

protected $fillable = [
    'topic_id',
    'title',
    'description',
    'content',
    'visible',
    'sortorder',
];
/**
     * Relasi ke model Topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
public function topic()
{
    return $this->belongsTo(Topic::class, 'topic_id', 'id');
}

public function assigns()
    {
        return $this->hasMany(LomAssign::class, 'subtopic_id', 'id');
    }
public function files()
    {
        return $this->hasMany(LomFile::class, 'subtopic_id', 'id');
    }

    public function folders()
    {
        return $this->hasMany(LomFolder::class, 'subtopic_id', 'id');
    }
    public function forums()
    {
        return $this->hasMany(LomForum::class, 'subtopic_id', 'id');
    }

    public function infographics()
    {
        return $this->hasMany(LomInfographic::class, 'subtopic_id', 'id');

    }
    public function labels()
    {
        return $this->hasMany(LomLabel::class, 'subtopic_id', 'id');
    }
    public function lessons()
    {
        return $this->hasMany(LomLesson::class, 'subtopic_id', 'id');
    }
    public function pages()
    {
        return $this->hasMany(LomPage::class, 'subtopic_id', 'id');
    }
      public function quizs()
    {
        return $this->hasMany(LomQuiz::class, 'subtopic_id', 'id');
    }

    public function urls()
    {
        return $this->hasMany(LomUrl::class, 'subtopic_id', 'id');
    }
}
