<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add plain columns if missing
        Schema::table('match_requests', function (Blueprint $t) {
            if (! Schema::hasColumn('match_requests', 'pair_min')) {
                $t->unsignedBigInteger('pair_min')->nullable()->after('to_company_id');
            }
            if (! Schema::hasColumn('match_requests', 'pair_max')) {
                $t->unsignedBigInteger('pair_max')->nullable()->after('pair_min');
            }
        });

        // 2) Backfill once using IF() (works on MySQL & MariaDB)
        DB::statement("
            UPDATE match_requests
            SET
                pair_min = IF(from_company_id < to_company_id, from_company_id, to_company_id),
                pair_max = IF(from_company_id > to_company_id, from_company_id, to_company_id)
            WHERE pair_min IS NULL OR pair_max IS NULL
        ");

        // 3) Add indexes (guarded to avoid dup-index errors if migration partially ran earlier)
        $hasIndex = function (string $table, string $keyName): bool {
            $rows = DB::select("SHOW INDEX FROM `$table` WHERE Key_name = ?", [$keyName]);
            return !empty($rows);
        };

        Schema::table('match_requests', function (Blueprint $t) use ($hasIndex) {
            if (! $hasIndex('match_requests', 'mr_to_status')) {
                $t->index(['to_company_id', 'status'], 'mr_to_status');
            }
            if (! $hasIndex('match_requests', 'mr_from_status')) {
                $t->index(['from_company_id', 'status'], 'mr_from_status');
            }
            if (! $hasIndex('match_requests', 'mr_created_at')) {
                $t->index('created_at', 'mr_created_at');
            }
            if (! $hasIndex('match_requests', 'uniq_match_pairs_per_status')) {
                $t->unique(['pair_min', 'pair_max', 'status'], 'uniq_match_pairs_per_status');
            }
        });
    }

    public function down(): void
    {
        $hasIndex = function (string $table, string $keyName): bool {
            $rows = DB::select("SHOW INDEX FROM `$table` WHERE Key_name = ?", [$keyName]);
            return !empty($rows);
        };

        Schema::table('match_requests', function (Blueprint $t) use ($hasIndex) {
            if ($hasIndex('match_requests', 'mr_to_status')) {
                $t->dropIndex('mr_to_status');
            }
            if ($hasIndex('match_requests', 'mr_from_status')) {
                $t->dropIndex('mr_from_status');
            }
            if ($hasIndex('match_requests', 'mr_created_at')) {
                $t->dropIndex('mr_created_at');
            }
            if ($hasIndex('match_requests', 'uniq_match_pairs_per_status')) {
                $t->dropUnique('uniq_match_pairs_per_status');
            }
        });

        Schema::table('match_requests', function (Blueprint $t) {
            if (Schema::hasColumn('match_requests', 'pair_min')) {
                $t->dropColumn('pair_min');
            }
            if (Schema::hasColumn('match_requests', 'pair_max')) {
                $t->dropColumn('pair_max');
            }
        });
    }
};
