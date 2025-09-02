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
Schema::create('devices', function (Blueprint $t) {
    $t->id();
    $t->foreignId('company_id')->constrained('teams')->cascadeOnDelete();
    $t->string('name');
    $t->string('slug')->unique();
    $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $t->text('description')->nullable();
    $t->string('indications')->nullable();
    $t->enum('fda_pathway', ['none','exempt','510k','pma'])->default('none')->index();
    $t->boolean('reimbursable')->default(false)->index();
    $t->decimal('margin_target', 5, 2)->nullable();
    $t->enum('status', ['draft','listed','paused'])->default('draft')->index();
    $t->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
