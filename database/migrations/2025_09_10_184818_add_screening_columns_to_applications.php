<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // verdict (nullable) + small counts + override flag + rules hash + answers JSON
            if (! Schema::hasColumn('applications', 'screening_verdict')) {
                $table->string('screening_verdict')->nullable()->index();
            }

            if (! Schema::hasColumn('applications', 'screening_fail_count')) {
                $table->unsignedSmallInteger('screening_fail_count')->default(0);
            }

            if (! Schema::hasColumn('applications', 'screening_flag_count')) {
                $table->unsignedSmallInteger('screening_flag_count')->default(0);
            }

            if (! Schema::hasColumn('applications', 'screening_overridden')) {
                $table->boolean('screening_overridden')->default(false);
            }

            if (! Schema::hasColumn('applications', 'screening_rules_hash')) {
                $table->string('screening_rules_hash', 64)->nullable()->index();
            }

            if (! Schema::hasColumn('applications', 'screening_answers')) {
                $table->json('screening_answers')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'screening_verdict')) {
                $table->dropColumn('screening_verdict');
            }
            if (Schema::hasColumn('applications', 'screening_fail_count')) {
                $table->dropColumn('screening_fail_count');
            }
            if (Schema::hasColumn('applications', 'screening_flag_count')) {
                $table->dropColumn('screening_flag_count');
            }
            if (Schema::hasColumn('applications', 'screening_overridden')) {
                $table->dropColumn('screening_overridden');
            }
            if (Schema::hasColumn('applications', 'screening_rules_hash')) {
                $table->dropColumn('screening_rules_hash');
            }
            if (Schema::hasColumn('applications', 'screening_answers')) {
                $table->dropColumn('screening_answers');
            }
        });
    }
};
