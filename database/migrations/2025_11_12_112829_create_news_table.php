<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('excerpt', 300)->nullable();
            $table->longText('content');

            $table->string('cover_path')->nullable();   // storage/app/public 경로
            $table->string('source_url')->nullable();   // 원문 링크(옵션)

            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('views')->default(0);

            $table->timestamps();

            $table->index(['published_at', 'is_pinned']);
        });
    }
    public function down(): void { Schema::dropIfExists('news'); }
};
