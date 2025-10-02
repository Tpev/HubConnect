<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // e.g. 'orthopedics', 'wound_care'
            $table->string('name');             // Display name
            $table->string('parent_code')->nullable(); // optional hierarchy (e.g. orthopedics->spine)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('specialties');
    }
};
