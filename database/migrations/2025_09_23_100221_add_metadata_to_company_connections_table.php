<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('company_connections', function (Blueprint $table) {
            // JSON column to store misc info (deal_room_id, timestamps, flags, etc.)
            if (!Schema::hasColumn('company_connections', 'metadata')) {
                $table->json('metadata')->nullable()->after('company_b_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_connections', function (Blueprint $table) {
            if (Schema::hasColumn('company_connections', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
