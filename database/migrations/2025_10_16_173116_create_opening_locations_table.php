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
    Schema::create('opening_locations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('opening_id')->constrained()->cascadeOnDelete();
        $table->enum('entity_type', ['country','state','city']);
        $table->unsignedBigInteger('entity_id');
        $table->timestamps();
        $table->unique(['opening_id','entity_type','entity_id'], 'uniq_opening_location');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_locations');
    }
};
