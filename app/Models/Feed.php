<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Feed extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content',
    ];

    protected $appends = ['liked'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getLikedAttribute(): bool
    {
        // return (bool) $this->likes()->where('feed_id', $this->id)->where('user_id', auth()->id())->exists();
        return (bool) $this->likes()->where('feed_id', $this->id)->where('user_id', Auth::id())->exists();
    }

    // Eliminar comentarios en cascada
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($feed) {
            $feed->comments()->delete();
            $feed->likes()->delete();
        });
    }
}
