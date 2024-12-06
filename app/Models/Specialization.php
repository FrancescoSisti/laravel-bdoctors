<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get the users that belong to this specialization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'specialization_user');
    }

    /**
     * Get the profiles associated with users of this specialization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function profiles()
    {
        return $this->hasManyThrough(
            Profile::class,
            User::class,
            'id', // Local key on users table
            'user_id', // Foreign key on profiles table
            'id', // Local key on specializations table
            'id' // Foreign key on users table
        );
    }
}
