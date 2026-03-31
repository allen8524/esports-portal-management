<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('players', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('name');                 // 실명
        $table->string('ign')->index();         // 인게임 닉네임
        $table->string('slug')->unique();       // URL 슬러그
        $table->string('role', 20)->nullable(); // Top/Jungle/Mid/ADC/Support
        $table->string('country', 2)->nullable(); // ISO-2 (KR, US ...)
        $table->date('birthdate')->nullable();

        // 아직 teams 테이블 안 만들었으니 FK는 나중에 추가. 우선 컬럼만.
        $table->unsignedBigInteger('team_id')->nullable();

        $table->string('photo_url')->nullable(); // 이미지 경로
        $table->boolean('is_active')->default(true);
        $table->date('joined_at')->nullable();
        $table->date('left_at')->nullable();
        $table->json('meta')->nullable();        // 소셜 링크 등 메타
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('players');
}

};
