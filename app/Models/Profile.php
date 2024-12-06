<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'curriculum',
        'photo',
        'office_address',
        'phone',
        'services'
    ];

    /**
     * Get the user that owns the profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for the profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the sponsorships for the profile.
     * Includes pivot table data for start_date and end_date.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sponsorships()
    {
        return $this->belongsToMany(Sponsorship::class)
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }
}
