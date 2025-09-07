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
    Schema::create('roleplay_scenario_packs', function (Blueprint $t) {
        $t->id();
        $t->string('name');
        $t->text('description')->nullable();
        $t->json('config')->nullable(); // scenario ids, scoring weights
        $t->boolean('is_active')->default(true);
        $t->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roleplay_scenario_packs');
    }
};
