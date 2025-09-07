<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add the column as nullable so we can backfill
        Schema::table('applications', function (Blueprint $t) {
            $t->unsignedBigInteger('team_id')->nullable()->after('id');
        });

        // 2) Backfill from openings.team_id
        DB::statement('UPDATE applications a JOIN openings o ON a.opening_id = o.id SET a.team_id = o.team_id');

        // 3) Add FK
        Schema::table('applications', function (Blueprint $t) {
            $t->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
        });

        // 4) Make it NOT NULL (no DBAL needed)
        DB::statement('ALTER TABLE applications MODIFY team_id BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            $t->dropForeign(['team_id']);
            $t->dropColumn('team_id');
        });
    }
};
