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
    Schema::create('geo_subdivisions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained('geo_countries')->cascadeOnDelete();
        $table->string('iso_3166_2')->unique(); // e.g. US-CA, FR-IDF
        $table->string('name');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_subdivisions');
    }
};
