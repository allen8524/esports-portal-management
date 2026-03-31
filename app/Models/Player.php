<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Player extends Model
{
    use HasFactory;

	protected $fillable = [
	  'name','ign','slug','role','country','birthdate','team_id',
	  'photo_url','photo_path',
	  'is_active','joined_at','left_at','meta',
	];


    protected $casts = [
        'birthdate' => 'date',
        'joined_at' => 'date',
        'left_at'   => 'date',
        'is_active' => 'boolean',
        'meta'      => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public static function resolvePhotoUrl(?string $photoUrl, ?string $photoPath = null): ?string
    {
        if (!empty($photoPath)) {
            return asset('storage/' . ltrim($photoPath, '/'));
        }

        if (empty($photoUrl)) {
            return null;
        }

        if (preg_match('#^https?://#', $photoUrl)) {
            $path = parse_url($photoUrl, PHP_URL_PATH);

            if (is_string($path) && preg_match('#(?:^|/)storage/(.+)$#', $path, $matches)) {
                return asset('storage/' . $matches[1]);
            }

            return $photoUrl;
        }

        if (preg_match('#(?:^|/)storage/(.+)$#', $photoUrl, $matches)) {
            return asset('storage/' . $matches[1]);
        }

        if (str_starts_with($photoUrl, '/')) {
            return asset(ltrim($photoUrl, '/'));
        }

        return asset('storage/' . ltrim($photoUrl, '/'));
    }

    public function getPhotoSrcAttribute(): ?string
    {
        return static::resolvePhotoUrl($this->photo_url, $this->photo_path);
    }
	public function getRouteKeyName(): string { return 'slug'; }

}
