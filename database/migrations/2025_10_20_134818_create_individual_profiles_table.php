<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('individual_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable(); // 0–255

            $table->json('skills')->nullable(); // ["Sales", "Orthopedics", ...]
            $table->json('links')->nullable();  // [{"label":"LinkedIn","url":"..."}, ...]

            $table->string('cv_path')->nullable(); // storage path for uploaded CV
            $table->enum('visibility', ['private','discoverable'])->default('private');

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('individual_profiles');
    }
};
