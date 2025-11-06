<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomForumPost extends Model
{
    use HasFactory;
    protected $table = 'lom_forum_posts';

    protected $fillable = [
        'forum_id',
        'user_id',
        'content',

    ];

    public function forum()
    {
        return $this->belongsTo(LomForum::class, 'forum_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
