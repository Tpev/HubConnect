<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Company capabilities/coverage (company_id references teams.id)
        if (!Schema::hasTable('company_specialty')) {
            Schema::create('company_specialty', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('specialty_id');
                $table->primary(['company_id','specialty_id']);
            });
        }

        if (!Schema::hasTable('company_territory')) {
            Schema::create('company_territory', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('territory_id');
                $table->primary(['company_id','territory_id']);
            });
        }

        if (!Schema::hasTable('company_facility_type')) {
            Schema::create('company_facility_type', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('facility_type_id');
                $table->primary(['company_id','facility_type_id']);
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('company_specialty');
        Schema::dropIfExists('company_territory');
        Schema::dropIfExists('company_facility_type');
    }
};
