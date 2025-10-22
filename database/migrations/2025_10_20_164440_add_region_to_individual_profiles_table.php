<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('individual_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('individual_profiles', 'region')) {
                $table->string('region', 120)->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('individual_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('individual_profiles', 'region')) {
                $table->dropColumn('region');
            }
        });
    }
};
