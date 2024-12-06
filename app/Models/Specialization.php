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
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the profiles associated with users of this specialization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function profiles()
    {
        return $this->hasManyThrough(Profile::class, User::class);
    }
}