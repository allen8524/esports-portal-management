<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatchNote;

class PatchNoteSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['version'=>'25.22','title'=>'25.22 패치 노트','date'=>now()->subDays(1)],
            ['version'=>'25.21','title'=>'25.21 패치 노트','date'=>now()->subDays(8)],
            ['version'=>'25.20','title'=>'25.20 패치 노트','date'=>now()->subDays(15)],
            ['version'=>'25.19','title'=>'25.19 패치 노트','date'=>now()->subDays(22)],
        ];

        foreach ($rows as $r) {
            PatchNote::updateOrCreate(
                ['slug' => 'lol-'.$r['version']],
                [
                    'game'         => 'lol',
                    'version'      => $r['version'],
                    'title'        => $r['title'],
                    'published_at' => $r['date'],
                    'hero_image'   => 'img/patch/default.jpg',
                ]
            );
        }
    }
}
