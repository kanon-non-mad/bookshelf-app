<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'isbn',
        'title',
        'author',
        'published_at',
        'description',
        'book_image',
        ];
    
    protected $casts = [
        'published_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function favoritedUsers(): BelongsToMany
    {
    return $this->belongsToMany(User::class, 'favorites');
    }
}
