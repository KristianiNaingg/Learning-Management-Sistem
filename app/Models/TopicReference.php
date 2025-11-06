<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicReference extends Model
{
    use HasFactory;
    protected $table = 'topic_references';
    protected $fillable = [
        'topic_id',
        'content',

    ];

    /**
     * Relasi ke model Topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
