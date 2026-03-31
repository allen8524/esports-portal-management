<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug'];

    public function news() { return $this->hasMany(News::class); }

    protected static function booted(): void
    {
        static::saving(function (Category $cat) {
            if (!$cat->slug) $cat->slug = Str::slug($cat->name);
        });
    }
}
