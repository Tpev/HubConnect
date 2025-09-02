<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('teams', function (Blueprint $t) {
            $t->enum('company_type', ['manufacturer','distributor'])->nullable()->index();
            $t->string('website')->nullable();
            $t->string('phone')->nullable();
            $t->text('about')->nullable();
            $t->string('hq_state')->nullable();
            $t->string('hq_country')->default('US');
        });
    }
    public function down(): void {
        Schema::table('teams', function (Blueprint $t) {
            $t->dropColumn(['company_type','website','phone','about','hq_state','hq_country']);
        });
    }
};
