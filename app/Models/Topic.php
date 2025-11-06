<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
     protected $table = 'topics';
     protected $fillable = [
        'course_id',
        'title',
        'description',
        'visible',
        'sort_order',
        'sub_cpmk',
    ];


/**
     * Relasi ke model Course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * Relasi ke model Subtopic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function subtopics()
    {
        return $this->hasMany(Subtopic::class, 'topic_id');
    }


    public function references()
    {
        return $this->hasMany(TopicReference::class, 'topic_id');
    }

}
