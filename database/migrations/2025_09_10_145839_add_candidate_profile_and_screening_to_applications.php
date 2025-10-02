<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            // Candidate profile (individual columns + aggregated JSON)
            $t->unsignedTinyInteger('years_total')->nullable();
            $t->unsignedTinyInteger('years_med_device')->nullable();
            $t->json('candidate_specialties')->nullable(); // array of strings
            $t->string('state', 64)->nullable();           // based state/territory
            $t->unsignedTinyInteger('travel_percent_max')->nullable();
            $t->boolean('overnight_ok')->nullable();
            $t->boolean('driver_license')->nullable();

            $t->json('opening_type_accepts')->nullable();      // array of enum values
            $t->json('comp_structure_accepts')->nullable();    // array of enum values
            $t->unsignedInteger('expected_base')->nullable();  // USD
            $t->unsignedInteger('expected_ote')->nullable();   // USD

            $t->boolean('cold_outreach_ok')->nullable();
            $t->string('work_auth', 40)->nullable();
            $t->date('start_date')->nullable();

            $t->boolean('has_noncompete_conflict')->nullable();
            $t->boolean('background_check_ok')->nullable();

            // Aggregated candidate profile as JSON (optional snapshot)
            $t->json('candidate_profile')->nullable();

            // Screening outcome
            $t->json('screening_result')->nullable();      // {pass, fail_count, flag_count, fails[], flags[]}
            $t->boolean('screening_pass')->nullable();
            $t->unsignedSmallInteger('screening_fail_count')->default(0);
            $t->unsignedSmallInteger('screening_flag_count')->default(0);
            $t->timestamp('auto_rejected_at')->nullable();

            // Helpful indexes
            $t->index('state');
            $t->index('screening_pass');
            $t->index('auto_rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            $t->dropColumn([
                'years_total','years_med_device','candidate_specialties','state','travel_percent_max','overnight_ok',
                'driver_license','opening_type_accepts','comp_structure_accepts','expected_base','expected_ote',
                'cold_outreach_ok','work_auth','start_date','has_noncompete_conflict','background_check_ok',
                'candidate_profile','screening_result','screening_pass','screening_fail_count','screening_flag_count',
                'auto_rejected_at',
            ]);
        });
    }
};
