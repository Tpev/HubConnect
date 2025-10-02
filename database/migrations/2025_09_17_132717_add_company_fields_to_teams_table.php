<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('company_type')->nullable()->after('slug'); // 'manufacturer'|'distributor'|'both'
            $table->string('website')->nullable()->after('company_type');
            $table->string('hq_country', 2)->nullable()->after('website'); // ISO 3166-1 alpha-2
            $table->unsignedSmallInteger('year_founded')->nullable()->after('hq_country');
            $table->unsignedInteger('headcount')->nullable()->after('year_founded');
            $table->string('stage')->nullable()->after('headcount'); // 'startup','growth','established','global'
            $table->text('summary')->nullable()->after('stage');
            // Jetstream often has team_profile_photo_path; keep using it for logo if present
        });
    }

    public function down(): void {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'slug','company_type','website','hq_country',
                'year_founded','headcount','stage','summary'
            ]);
        });
    }
};
