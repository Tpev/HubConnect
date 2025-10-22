<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add column + FK if missing
        if (! Schema::hasColumn('applications', 'candidate_user_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('candidate_user_id')
                    ->nullable()
                    ->after('opening_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        // 2) Add unique index for (candidate_user_id, opening_id) if missing
        $indexName = 'applications_candidate_user_id_opening_id_unique';

        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'applications')
            ->where('index_name', $indexName)
            ->exists();

        if (! $indexExists) {
            Schema::table('applications', function (Blueprint $table) use ($indexName) {
                // Allows multiple guest applications (NULL candidate_user_id), but 1 per user/opening
                $table->unique(['candidate_user_id', 'opening_id'], $indexName);
            });
        }
    }

    public function down(): void
    {
        $indexName = 'applications_candidate_user_id_opening_id_unique';

        // Drop the unique index if it exists
        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'applications')
            ->where('index_name', $indexName)
            ->exists();

        if ($indexExists) {
            Schema::table('applications', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }

        // Drop FK + column if present
        if (Schema::hasColumn('applications', 'candidate_user_id')) {
            Schema::table('applications', function (Blueprint $table) {
                // drop FK by column reference (portable)
                $table->dropConstrainedForeignId('candidate_user_id');
            });
        }
    }
};
