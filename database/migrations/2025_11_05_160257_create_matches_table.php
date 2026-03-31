<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('matches', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title')->nullable();

            // 팀 FK (teams 테이블이 이미 있어야 함)
            $t->foreignId('team1_id')->constrained('teams');
            $t->foreignId('team2_id')->constrained('teams');

            $t->unsignedTinyInteger('best_of')->default(3);   // 1/3/5/7
            $t->dateTime('start_at')->nullable();

            // 상태
            $t->enum('status', ['scheduled','live','finished','canceled'])
              ->default('scheduled');

            // 스코어 & 승자
            $t->unsignedTinyInteger('team1_score')->default(0);
            $t->unsignedTinyInteger('team2_score')->default(0);
            $t->foreignId('winner_team_id')->nullable()->constrained('teams');

            // 메타
            $t->string('stage')->nullable();   // 예: Group A, Playoffs QF
            $t->string('league')->nullable();  // 예: LCK, Worlds 등
            $t->string('vod_url')->nullable();
            $t->text('notes')->nullable();

            $t->timestamps();

            $t->index(['start_at','status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('matches');
    }
};

