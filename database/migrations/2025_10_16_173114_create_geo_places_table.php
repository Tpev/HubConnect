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
    Schema::create('geo_places', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained('geo_countries')->cascadeOnDelete();
        $table->foreignId('subdivision_id')->nullable()->constrained('geo_subdivisions')->nullOnDelete();
        $table->string('ext_source', 20)->nullable(); // google
        $table->string('ext_id', 64)->nullable()->index(); // place_id
        $table->string('name');
        $table->string('ascii_name')->nullable();
        $table->decimal('lat', 10, 7)->nullable();
        $table->decimal('lng', 10, 7)->nullable();
        $table->timestamp('captured_at')->nullable();
        $table->timestamps();

        $table->unique(['ext_source','ext_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_places');
    }
};
