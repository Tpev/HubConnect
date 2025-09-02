<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('favorite_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('company_id'); // teams.id (distributor)
            $table->timestamps();

            $table->unique(['device_id','company_id']);
            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('teams')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('favorite_devices');
    }
};
