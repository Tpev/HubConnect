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
    Schema::create('applications', function (Blueprint $t) {
        $t->id();
        $t->foreignId('opening_id')->constrained('openings');
        $t->foreignId('candidate_user_id')->nullable()->constrained('users');
        $t->string('name');
        $t->string('email');
        $t->string('phone')->nullable();
        $t->string('linkedin')->nullable();
        $t->string('cv_path')->nullable(); // storage/app/private/...
        $t->text('notes')->nullable();
        $t->string('status')->default('new'); // new|reviewed|shortlisted|interview|offer|hired|rejected
        $t->enum('roleplay_status', ['pending','invited','in_progress','completed','expired','waived'])->default('pending')->index();
        $t->decimal('roleplay_score', 5, 2)->nullable()->index();
        $t->json('roleplay_summary')->nullable();
        $t->unsignedInteger('roleplay_attempts')->default(0);
        $t->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
