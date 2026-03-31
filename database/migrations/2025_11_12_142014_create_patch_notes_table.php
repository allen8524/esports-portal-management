<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_patch_notes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('patch_notes', function (Blueprint $t) {
            $t->id();
            $t->string('game', 16)->default('lol')->index(); // 예: lol
            $t->string('version', 32)->nullable()->index();  // 예: 25.22
            $t->string('title');                             // 카드 타이틀
            $t->string('slug')->unique();                    // 상세 페이지용
            $t->timestamp('published_at')->nullable()->index();
            $t->string('hero_image')->nullable();            // 카드 배경 이미지
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('patch_notes');
    }
};
