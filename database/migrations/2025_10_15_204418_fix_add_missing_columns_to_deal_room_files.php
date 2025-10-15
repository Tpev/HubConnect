<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deal_room_files', function (Blueprint $table) {
            // Add missing columns safely (idempotent guards)
            if (! Schema::hasColumn('deal_room_files', 'name')) {
                $table->string('name')->after('path');
            }
            if (! Schema::hasColumn('deal_room_files', 'type')) {
                $table->string('type')->nullable()->after('name');
            }
            if (! Schema::hasColumn('deal_room_files', 'size')) {
                $table->unsignedBigInteger('size')->default(0)->after('type');
            }
            if (! Schema::hasColumn('deal_room_files', 'uploaded_by')) {
                $table->foreignId('uploaded_by')
                    ->nullable()
                    ->after('size')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Skip index existence detection (no Doctrine). If you need the index and it doesn’t exist, run a separate migration later.
            // $table->index(['room_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('deal_room_files', function (Blueprint $table) {
            if (Schema::hasColumn('deal_room_files', 'uploaded_by')) {
                // drops FK + column on recent Laravel; if your version doesn’t, drop foreign key then column
                $table->dropConstrainedForeignId('uploaded_by');
            }
            if (Schema::hasColumn('deal_room_files', 'size')) {
                $table->dropColumn('size');
            }
            if (Schema::hasColumn('deal_room_files', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('deal_room_files', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
