<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add `path` only if it does not exist
        if (! Schema::hasColumn('deal_room_files', 'path')) {
            Schema::table('deal_room_files', function (Blueprint $table) {
                $table->string('path')->after('room_id');
            });
        }
    }

    public function down(): void
    {
        // Safe rollback
        if (Schema::hasColumn('deal_room_files', 'path')) {
            Schema::table('deal_room_files', function (Blueprint $table) {
                $table->dropColumn('path');
            });
        }
    }
};
