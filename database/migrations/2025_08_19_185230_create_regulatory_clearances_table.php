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
Schema::create('regulatory_clearances', function (Blueprint $t) {
    $t->id();
    $t->foreignId('device_id')->constrained()->cascadeOnDelete();
    $t->enum('clearance_type', ['510k','pma','exempt']);
    $t->string('number')->nullable();
    $t->date('issue_date')->nullable();
    $t->string('link')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_clearances');
    }
};
