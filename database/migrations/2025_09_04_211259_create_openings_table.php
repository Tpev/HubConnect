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
    Schema::create('openings', function (Blueprint $t) {
        $t->id();
        $t->foreignId('team_id')->constrained(); // employer team (manufacturer|distributor)
        $t->string('title');
        $t->string('slug')->unique();
        $t->text('description');
        $t->string('company_type'); // manufacturer|distributor
        $t->json('specialty_ids')->nullable();
        $t->json('territory_ids')->nullable();
        $t->string('compensation')->nullable();
        $t->timestamp('visibility_until')->nullable();
        $t->string('status')->default('published'); // draft|published|archived
        // Roleplay policy
        $t->enum('roleplay_policy', ['disabled','optional','required'])->default('optional')->index();
        $t->foreignId('roleplay_scenario_pack_id')->nullable()->constrained('roleplay_scenario_packs')->nullOnDelete();
        $t->decimal('roleplay_pass_threshold', 5, 2)->nullable();
        $t->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openings');
    }
};
