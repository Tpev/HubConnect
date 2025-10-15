<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'team_profile_photo_path')) {
                // place it somewhere reasonable in your schema
                $table->string('team_profile_photo_path')->nullable()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'team_profile_photo_path')) {
                $table->dropColumn('team_profile_photo_path');
            }
        });
    }
};
