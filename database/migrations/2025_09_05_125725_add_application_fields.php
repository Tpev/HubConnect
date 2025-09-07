<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            // Core applicant fields
            if (! Schema::hasColumn('applications', 'candidate_name')) {
                $t->string('candidate_name', 120)->after('opening_id');
            }
            if (! Schema::hasColumn('applications', 'email')) {
                $t->string('email', 150)->after('candidate_name');
            }
            if (! Schema::hasColumn('applications', 'phone')) {
                $t->string('phone', 40)->nullable()->after('email');
            }
            if (! Schema::hasColumn('applications', 'location')) {
                $t->string('location', 120)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('applications', 'cover_letter')) {
                $t->text('cover_letter')->nullable()->after('location');
            }
            if (! Schema::hasColumn('applications', 'cv_path')) {
                $t->string('cv_path', 255)->nullable()->after('cover_letter');
            }

            // Workflow fields
            if (! Schema::hasColumn('applications', 'status')) {
                $t->string('status', 30)->default('new')->after('cv_path');
            }
            if (! Schema::hasColumn('applications', 'score')) {
                $t->decimal('score', 5, 2)->nullable()->after('status');
            }

            // Roleplay lifecycle
            if (! Schema::hasColumn('applications', 'invited_at')) {
                $t->timestamp('invited_at')->nullable()->after('score');
            }
            if (! Schema::hasColumn('applications', 'invite_token')) {
                $t->string('invite_token', 80)->nullable()->after('invited_at')->index();
            }
            if (! Schema::hasColumn('applications', 'completed_at')) {
                $t->timestamp('completed_at')->nullable()->after('invite_token');
            }
            if (! Schema::hasColumn('applications', 'roleplay_score')) {
                $t->decimal('roleplay_score', 5, 2)->nullable()->after('completed_at');
            }

            // Helpful indexes (optional)
            if (! Schema::hasColumn('applications', 'email')) {
                $t->index('email');
            }
            if (! Schema::hasColumn('applications', 'status')) {
                $t->index('status');
            }
            if (! Schema::hasColumn('applications', 'opening_id')) {
                $t->index('opening_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            // Drop in reverse if you want; usually you can leave them
            $cols = [
                'candidate_name','email','phone','location','cover_letter','cv_path',
                'status','score','invited_at','invite_token','completed_at','roleplay_score',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('applications', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
