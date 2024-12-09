<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'user_id'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * Get the user that owns the upload.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL for the uploaded file
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Delete the file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($upload) {
            Storage::delete($upload->file_path);
        });
    }
}
