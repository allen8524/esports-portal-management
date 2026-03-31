<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','title','slug','excerpt','content',
        'cover_path','source_url','is_pinned','published_at'
    ];

    protected $casts = [
        'is_pinned'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string { return 'slug'; }

    public function category(){ return $this->belongsTo(Category::class); }

    // 스코프
    public function scopePublished($q){ return $q->whereNotNull('published_at')->where('published_at','<=',now()); }
    public function scopeSearch($q, $kw){
        if(!$kw) return $q;
        return $q->where(function($w) use ($kw){
            $w->where('title','like',"%$kw%")
              ->orWhere('excerpt','like',"%$kw%")
              ->orWhere('content','like',"%$kw%");
        });
    }

    protected static function booted(): void
    {
        static::saving(function (News $n) {
            if (!$n->slug) $n->slug = Str::slug(Str::limit($n->title, 60, ''));
        });
    }
}
