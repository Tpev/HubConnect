<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company_specialties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained()->cascadeOnDelete();
            $table->string('depth')->default('primary'); // 'primary'|'secondary'
            $table->timestamps();
            $table->unique(['company_id','specialty_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('company_specialties');
    }
};
