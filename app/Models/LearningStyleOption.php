<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningStyleOption extends Model
{
    use HasFactory;


    protected $table = 'learning_style_options';

    protected $fillable = [
        'style_option_name',
        'learning_dimensions_id',
        'description',
    ];

    /**
     * Relasi belongs-to dengan LearningDimension
     * Satu opsi gaya belajar milik satu dimensi
     */
    public function dimension()
    {
        return $this->belongsTo(LearningDimension::class, 'learning_dimensions_id');
    }

    /**
     * Relasi many-to-many dengan User melalui tabel pivot user_learning_style_options
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_learning_style_options', 'learning_style_option_id', 'user_id')
                    ->withPivot('dimension', 'a_count', 'b_count', 'final_score', 'category', 'description','created_at', 'updated_at')
                    ->withTimestamps();
    }

}

