<?php

// app/Models/PatchNote.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatchNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'game', 'version', 'title', 'slug', 'published_at', 'hero_image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // 라우트 바인딩: /patch-notes/{slug}
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Scopes */
    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeGame($q, $game = 'lol')
    {
        return $q->where('game', $game);
    }
}
