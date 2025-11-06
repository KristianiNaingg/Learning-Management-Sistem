<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningDimension extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'learning_dimensions';

    protected $fillable = [
        'style_name',
        'dimension',
        'description',
    ];

    /**
     * Relasi one-to-many dengan LearningStyleOption
     * Satu dimensi memiliki banyak opsi gaya belajar (misalnya, Processing -> Active, Reflective)
     */
    public function options()
    {
        return $this->hasMany(LearningStyleOption::class, 'learning_dimensions_id');
    }

}
