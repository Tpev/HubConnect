<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // If table missing, bail.
        if (! Schema::hasTable('deal_room_files')) return;

        // Case A: legacy column is "deal_room_id" → rename/backfill to "room_id"
        if (Schema::hasColumn('deal_room_files', 'deal_room_id') && ! Schema::hasColumn('deal_room_files', 'room_id')) {
            // Add new column first (nullable for backfill)
            Schema::table('deal_room_files', function (Blueprint $table) {
                $table->unsignedBigInteger('room_id')->nullable()->after('id');
            });

            // Backfill values
            DB::table('deal_room_files')->update([
                'room_id' => DB::raw('deal_room_id')
            ]);

            // Add FK & index
            Schema::table('deal_room_files', function (Blueprint $table) {
                $table->index(['room_id', 'created_at']);
                // Drop old FK if it exists (best-effort; ignore if not found)
                try {
                    $table->dropForeign(['deal_room_id']);
                } catch (\Throwable $e) { /* noop */ }

                $table->foreign('room_id')->references('id')->on('deal_rooms')->cascadeOnDelete();
            });

            // Make not-null and drop old column
            Schema::table('deal_room_files', function (Blueprint $table) {
                $table->unsignedBigInteger('room_id')->nullable(false)->change();
                $table->dropColumn('deal_room_id');
            });
        }

        // Case B: neither column exists (fresh install gone wrong) → add "room_id"
        if (! Schema::hasColumn('deal_room_files', 'room_id')) {
            Schema::table('deal_room_files', function (Blueprint $table) {
                $table->unsignedBigInteger('room_id')->after('id');
                $table->index(['room_id', 'created_at']);
                $table->foreign('room_id')->references('id')->on('deal_rooms')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // We won't revert; safe no-op.
    }
};
