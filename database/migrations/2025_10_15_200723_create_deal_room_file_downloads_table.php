<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deal_room_file_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('deal_room_files')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('company_id')->nullable(); // for audit context
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index(['file_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_room_file_downloads');
    }
};
