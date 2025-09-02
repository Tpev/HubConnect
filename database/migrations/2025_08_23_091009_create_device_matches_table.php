<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('device_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('manufacturer_id'); // teams.id
            $table->unsignedBigInteger('distributor_id');  // teams.id

            $table->enum('status', ['pending','accepted','rejected','withdrawn','blocked'])->default('pending');
            $table->enum('initiator', ['manufacturer','distributor'])->index();

            $table->json('requested_territory_ids')->nullable();
            $table->boolean('exclusivity')->default(false);
            $table->decimal('proposed_commission_percent',5,2)->nullable();
            $table->text('message')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
            // For teams table (Jetstream)
            $table->foreign('manufacturer_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('distributor_id')->references('id')->on('teams')->cascadeOnDelete();

            $table->unique(['device_id','distributor_id']); // one active thread per pair
        });
    }

    public function down(): void {
        Schema::dropIfExists('device_matches');
    }
};
