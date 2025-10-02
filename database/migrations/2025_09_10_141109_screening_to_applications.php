<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'answers')) {
                $table->json('answers')->nullable()->after('cv_path');
            }
            $table->string('screen_status')->default('pending')->after('answers'); // pending|pass|fail|flag
            $table->json('screen_results')->nullable()->after('screen_status');
            $table->json('screen_reasons')->nullable()->after('screen_results');
            $table->timestamp('screen_failed_at')->nullable()->after('screen_reasons');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['screen_status', 'screen_results', 'screen_reasons', 'screen_failed_at']);
            if (Schema::hasColumn('applications', 'answers')) {
                $table->dropColumn('answers');
            }
        });
    }
};
