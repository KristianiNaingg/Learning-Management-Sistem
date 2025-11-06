<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LomUserLog extends Model
{
    use HasFactory;

    protected $table = 'lom_user_logs';

    protected $fillable = [
        'user_id',
        'lom_id',
        'lom_type',
        'action',
        'duration',
        'accessed_at',
    ];
     public $timestamps = false;

   

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke berbagai jenis LOM (Page, Quiz, File, Lesson, dll)
     * karena kamu bilang tidak mau pakai lom_type, maka langsung ke masing-masing LOM.
     * Misalnya kita ambil contoh ke LOM Page.
     */
    public function lomPage()
    {
        return $this->belongsTo(LomPage::class, 'lom_id', 'id');
    }

    public function lomQuiz()
    {
        return $this->belongsTo(LomQuiz::class, 'lom_id', 'id');
    }

    public function lomFile()
    {
        return $this->belongsTo(LomFile::class, 'lom_id', 'id');
    }

    public function lomLesson()
    {
        return $this->belongsTo(LomLesson::class, 'lom_id', 'id');
    }

    public function lomLabel()
    {
        return $this->belongsTo(LomLabel::class, 'lom_id', 'id');
    }

    public function lomForum()
    {
        return $this->belongsTo(LomForum::class, 'lom_id', 'id');
    }

    public function lomInfographic()
    {
        return $this->belongsTo(LomInfographic::class, 'lom_id', 'id');
    }

    public function lomUrl()
    {
        return $this->belongsTo(LomUrl::class, 'lom_id', 'id');
    }
}
