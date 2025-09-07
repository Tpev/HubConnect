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
    Schema::create('roleplay_evaluations', function (Blueprint $t) {
        $t->id();
        $t->foreignId('application_id')->constrained('applications');
        $t->foreignId('scenario_pack_id')->constrained('roleplay_scenario_packs');
        $t->uuid('token')->unique();
        $t->enum('status',['invited','started','submitted','scored','expired','canceled'])->index();
        $t->timestamp('opened_at')->nullable();
        $t->timestamp('submitted_at')->nullable();
        $t->timestamp('scored_at')->nullable();
        $t->decimal('score', 5, 2)->nullable();
        $t->longText('dossier_text')->nullable();
        $t->longText('transcript')->nullable(); // json string of turns
        $t->json('rubric')->nullable();
        $t->json('metadata')->nullable();
        $t->timestamp('expires_at')->nullable()->index();
        $t->foreignId('created_by')->nullable()->constrained('users');
        $t->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roleplay_evaluations');
    }
};
