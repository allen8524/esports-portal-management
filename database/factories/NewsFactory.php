<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        return [
            'category_id' => null,
            'title'       => $title,
            'slug'        => Str::slug(Str::limit($title, 60, '')),
            'excerpt'     => $this->faker->sentence(18),
            'content'     => '<p>'.$this->faker->paragraph(6).'</p>',
            'cover_path'  => null,
            'source_url'  => $this->faker->optional()->url(),
            'is_pinned'   => $this->faker->boolean(10),
            'published_at'=> now()->subDays(rand(0,10))->subMinutes(rand(0,1440)),
            'views'       => rand(0,5000),
        ];
    }
}
