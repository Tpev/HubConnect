<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_a_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('company_b_id')->constrained('teams')->cascadeOnDelete();
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();

            $table->unique(['company_a_id','company_b_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('company_connections');
    }
};
