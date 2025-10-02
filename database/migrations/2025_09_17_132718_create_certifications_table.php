<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 'FDA_510K','CE_MDR','ISO_13485','ANVISA'
            $table->string('name');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('certifications');
    }
};
