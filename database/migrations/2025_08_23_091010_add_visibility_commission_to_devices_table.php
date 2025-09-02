<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('devices', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('company_id');
            $table->enum('visibility', ['public','verified_only','invite_only'])->default('public')->after('is_published');

            // optional commercial info
            $table->decimal('commission_percent',5,2)->nullable()->after('visibility');
            $table->text('commission_notes')->nullable()->after('commission_percent');
        });
    }

    public function down(): void {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['is_published','visibility','commission_percent','commission_notes']);
        });
    }
};
