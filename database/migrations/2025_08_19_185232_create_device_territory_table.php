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
Schema::create('device_territory', function (Blueprint $t) {
    $t->id();
    $t->foreignId('device_id')->constrained()->cascadeOnDelete();
    $t->foreignId('territory_id')->constrained()->cascadeOnDelete();
    $t->unique(['device_id','territory_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_territory');
    }
};
