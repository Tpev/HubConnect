<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('deal_rooms', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('company_small_id');
    $table->unsignedBigInteger('company_large_id');
    $table->unsignedBigInteger('created_by_company_id')->nullable();
    $table->json('meta')->nullable();
    $table->timestamp('closed_at')->nullable();
    $table->timestamps();

    $table->unique(['company_small_id', 'company_large_id'], 'deal_rooms_unique_pair');

    // 👇 Point to 'teams' instead of 'companies'
    $table->foreign('company_small_id')->references('id')->on('teams')->cascadeOnDelete();
    $table->foreign('company_large_id')->references('id')->on('teams')->cascadeOnDelete();
    $table->foreign('created_by_company_id')->references('id')->on('teams')->nullOnDelete();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('deal_rooms');
    }
};
