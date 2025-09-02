<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('device_documents', function (Blueprint $t) {
    $t->id();
    $t->foreignId('device_id')->constrained()->cascadeOnDelete();
    $t->enum('type', ['brochure','ifus','training','evidence']);
    $t->string('path');
    $t->string('original_name')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_documents');
    }
};
