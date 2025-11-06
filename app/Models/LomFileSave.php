<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LomFileSave extends Model
{
    use HasFactory;
    protected $table = 'lom_file_saves';

    protected $primaryKey = 'id';


    protected $fillable = [
        'folder_id',
        'file_id',
    ];


    public $timestamps = true;


    public function folder()
    {
        return $this->belongsTo(LomFolder::class, 'folder_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(LomFile::class, 'file_id', 'id');
    }
}
