<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deal_room_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('deal_rooms')->cascadeOnDelete();
            $table->string('path');               // storage path
            $table->string('name');               // original filename
            $table->string('type')->nullable();   // mime or simple extension
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['room_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_room_files');
    }
};
