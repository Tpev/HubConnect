<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('match_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_company_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('to_company_id')->constrained('teams')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending|accepted|declined|more_info
            $table->string('context')->nullable(); // e.g. "Spain + Ortho"
            $table->text('note')->nullable();      // short message
            $table->json('metadata')->nullable();  // room for extra fit data
            $table->timestamps();

            $table->index(['to_company_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('match_requests');
    }
};
