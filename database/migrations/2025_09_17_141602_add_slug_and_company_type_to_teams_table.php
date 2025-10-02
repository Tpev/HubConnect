<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_slug_and_company_type_to_teams_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams','slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('teams','company_type')) {
                $table->string('company_type')->nullable()->after('slug'); // manufacturer|distributor|both
            }
        });
    }
    public function down(): void {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams','company_type')) $table->dropColumn('company_type');
            if (Schema::hasColumn('teams','slug')) $table->dropColumn('slug');
        });
    }
};
