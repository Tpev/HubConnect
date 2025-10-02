<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deal_room_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_room_id');
            $table->unsignedBigInteger('company_id'); // teams.id
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('last_typing_at')->nullable();

            // Smart email digest preferences & throttling
            // modes: 'smart' (default), 'immediate' (not used here), 'daily', 'mute'
            $table->string('notify_mode')->default('smart');
            $table->integer('email_cooldown_minutes')->default(60);
            $table->timestamp('last_email_at')->nullable();
            $table->timestamp('last_daily_email_at')->nullable();

            $table->timestamps();

            $table->unique(['deal_room_id', 'company_id'], 'deal_room_participants_unique');
            $table->foreign('deal_room_id')->references('id')->on('deal_rooms')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('teams')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_room_participants');
    }
};
