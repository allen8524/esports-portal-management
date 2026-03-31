<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $cats = collect(['공지','대회','팀/선수','패치노트'])->map(function($n){
            return Category::firstOrCreate(['slug'=>\Str::slug($n)], ['name'=>$n]);
        });

        News::factory()->count(36)->create()->each(function($n) use ($cats){
            if (rand(0,1)) $n->update(['category_id'=>$cats->random()->id]);
        });
    }
}
