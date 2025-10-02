<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams')->cascadeOnDelete();
            $table->string('intent_type')->default('looking_for'); // future-proof
            $table->string('status')->default('active'); // active|archived
            $table->json('payload'); // { territories:[], specialties:[], deal:{}, capacity_note, urgency, notes }
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_to')->nullable();
            $table->timestamps();

            $table->index(['company_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('company_intents');
    }
};
