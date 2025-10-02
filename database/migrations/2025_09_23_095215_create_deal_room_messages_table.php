<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('deal_room_messages', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('deal_room_id');
    $table->unsignedBigInteger('company_id'); // sender's "team"
    $table->unsignedBigInteger('user_id')->nullable();
    $table->text('body');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();

    $table->index(['deal_room_id', 'created_at']);
    $table->foreign('deal_room_id')->references('id')->on('deal_rooms')->cascadeOnDelete();

    // 👇 Point to 'teams' instead of 'companies'
    $table->foreign('company_id')->references('id')->on('teams')->cascadeOnDelete();

    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('deal_room_messages');
    }
};
