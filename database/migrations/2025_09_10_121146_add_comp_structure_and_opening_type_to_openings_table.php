<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('openings', function (Blueprint $table) {
            // After 'compensation' to keep related fields together
            $table->enum('comp_structure', ['salary','commission','salary_commission','equities'])
                  ->nullable()
                  ->after('compensation');

            $table->enum('opening_type', ['w2','1099','contractor','partner'])
                  ->nullable()
                  ->after('comp_structure');
        });
    }

    public function down(): void
    {
        Schema::table('openings', function (Blueprint $table) {
            $table->dropColumn(['comp_structure', 'opening_type']);
        });
    }
};
