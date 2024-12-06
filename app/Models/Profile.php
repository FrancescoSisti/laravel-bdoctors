<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'curriculum',
        'photo',
        'office_address',
        'phone',
        'services'
    ];

    protected $with = ['user'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'photo' => 'string:255'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function sponsorships()
    {
        return $this->belongsToMany(Sponsorship::class)
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }
}
