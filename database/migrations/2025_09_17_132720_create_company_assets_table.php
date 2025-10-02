<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams')->cascadeOnDelete();
            $table->string('type'); // 'brochure','pitch_video','deck','datasheet'
            $table->string('title')->nullable();
            $table->string('url')->nullable(); // S3 or external
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('company_assets');
    }
};
