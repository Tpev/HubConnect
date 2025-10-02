<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'kyc_status')) {
                $table->string('kyc_status')->default('new')->index()->after('updated_at'); // new|pending_review|approved|rejected|suspended
            }
            if (!Schema::hasColumn('teams', 'kyc_submitted_at')) {
                $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status');
            }
            if (!Schema::hasColumn('teams', 'kyc_verified_at')) {
                $table->timestamp('kyc_verified_at')->nullable()->after('kyc_submitted_at');
            }
            if (!Schema::hasColumn('teams', 'kyc_reviewer_user_id')) {
                $table->unsignedBigInteger('kyc_reviewer_user_id')->nullable()->after('kyc_verified_at');
                $table->foreign('kyc_reviewer_user_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('teams', 'kyc_notes')) {
                $table->text('kyc_notes')->nullable()->after('kyc_reviewer_user_id');
            }

            // Safety: add basics if your schema missed them (no-ops if they already exist)
            if (!Schema::hasColumn('teams', 'hq_country')) {
                $table->string('hq_country')->nullable()->after('name');
            }
            if (!Schema::hasColumn('teams', 'website')) {
                $table->string('website')->nullable()->after('hq_country');
            }
            if (!Schema::hasColumn('teams', 'official_email_domain')) {
                $table->string('official_email_domain')->nullable()->after('website');
            }
            if (!Schema::hasColumn('teams', 'company_type')) {
                $table->string('company_type')->nullable()->after('official_email_domain'); // manufacturer|distributor|both
            }
            if (!Schema::hasColumn('teams', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('company_type');
            }
            if (!Schema::hasColumn('teams', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('registration_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'kyc_status')) $table->dropColumn('kyc_status');
            if (Schema::hasColumn('teams', 'kyc_submitted_at')) $table->dropColumn('kyc_submitted_at');
            if (Schema::hasColumn('teams', 'kyc_verified_at')) $table->dropColumn('kyc_verified_at');
            if (Schema::hasColumn('teams', 'kyc_reviewer_user_id')) {
                $table->dropForeign(['kyc_reviewer_user_id']);
                $table->dropColumn('kyc_reviewer_user_id');
            }
            if (Schema::hasColumn('teams', 'kyc_notes')) $table->dropColumn('kyc_notes');
            if (Schema::hasColumn('teams', 'hq_country')) $table->dropColumn('hq_country');
            if (Schema::hasColumn('teams', 'website')) $table->dropColumn('website');
            if (Schema::hasColumn('teams', 'official_email_domain')) $table->dropColumn('official_email_domain');
            if (Schema::hasColumn('teams', 'company_type')) $table->dropColumn('company_type');
            if (Schema::hasColumn('teams', 'registration_number')) $table->dropColumn('registration_number');
            if (Schema::hasColumn('teams', 'linkedin_url')) $table->dropColumn('linkedin_url');
        });
    }
};
