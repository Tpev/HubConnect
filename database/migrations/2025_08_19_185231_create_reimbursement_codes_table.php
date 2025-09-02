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
Schema::create('reimbursement_codes', function (Blueprint $t) {
    $t->id();
    $t->foreignId('device_id')->constrained()->cascadeOnDelete();
    $t->enum('code_type', ['CPT','HCPCS','DRG','ICD10']);
    $t->string('code');
    $t->string('description')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimbursement_codes');
    }
};
