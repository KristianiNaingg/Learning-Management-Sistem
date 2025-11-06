<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $table = 'roles';

     protected $fillable = ['name_role'];
        /**
     * Get the users associated with the role.
     */

    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}
