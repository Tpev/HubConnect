<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('openings', function (Blueprint $table) {
            $table->string('screening_policy')->default('off')->after('status'); // off|soft|hard
            $table->json('screening_rules')->nullable()->after('screening_policy');
        });
    }

    public function down(): void
    {
        Schema::table('openings', function (Blueprint $table) {
            $table->dropColumn(['screening_policy', 'screening_rules']);
        });
    }
};
