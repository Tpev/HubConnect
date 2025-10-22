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
    Schema::create('geo_aliases', function (Blueprint $table) {
        $table->id();
        $table->enum('entity_type', ['country','state','city']);
        $table->unsignedBigInteger('entity_id');
        $table->string('alias')->index();
        $table->timestamps();
        $table->index(['entity_type','entity_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_aliases');
    }
};
