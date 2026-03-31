<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name','slug','region','founded_at','logo_url','is_active','meta'
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'founded_at' => 'date',
        'meta'       => 'array',
    ];

    // 관계: 팀(1) - 선수(N)
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public static function resolveLogoUrl(?string $logoUrl): ?string
    {
        if (empty($logoUrl)) {
            return null;
        }

        if (preg_match('#^https?://#', $logoUrl)) {
            $path = parse_url($logoUrl, PHP_URL_PATH);

            if (is_string($path) && preg_match('#(?:^|/)storage/(.+)$#', $path, $matches)) {
                return asset('storage/' . $matches[1]);
            }

            return $logoUrl;
        }

        if (preg_match('#(?:^|/)storage/(.+)$#', $logoUrl, $matches)) {
            return asset('storage/' . $matches[1]);
        }

        if (str_starts_with($logoUrl, '/')) {
            return asset(ltrim($logoUrl, '/'));
        }

        return asset('storage/' . ltrim($logoUrl, '/'));
    }

    public function getLogoSrcAttribute(): ?string
    {
        return static::resolveLogoUrl($this->logo_url);
    }
	
}
