<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();     // 팀명 (예: T1, GEN.G)
            $table->string('slug')->unique();     // 슬러그 (URL용)
            $table->string('region', 10)->nullable();   // 지역/리그 (KR, LPL, LEC 등)
            $table->date('founded_at')->nullable();     // 창단일
            $table->string('logo_url')->nullable();     // 로고 이미지 경로(파일 업로드는 나중에)
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();     // SNS 링크 등
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
