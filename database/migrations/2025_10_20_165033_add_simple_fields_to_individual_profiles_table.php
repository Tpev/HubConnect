<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('individual_profiles', function (Blueprint $table) {
            $cols = Schema::getColumnListing('individual_profiles');

            if (!in_array('phone', $cols))            $table->string('phone', 50)->nullable()->after('location');
            if (!in_array('region', $cols))           $table->string('region', 120)->nullable()->after('location');
            if (!in_array('years_experience', $cols)) $table->unsignedTinyInteger('years_experience')->nullable()->after('region');
            if (!in_array('skills', $cols))           $table->json('skills')->nullable()->after('years_experience');
            if (!in_array('headline', $cols))         $table->string('headline', 140)->nullable()->after('skills');
            if (!in_array('bio', $cols))              $table->text('bio')->nullable()->after('headline');
            if (!in_array('completed_at', $cols))     $table->timestamp('completed_at')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('individual_profiles', function (Blueprint $table) {
            foreach (['phone','region','years_experience','skills','headline','bio','completed_at'] as $col) {
                if (Schema::hasColumn('individual_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
