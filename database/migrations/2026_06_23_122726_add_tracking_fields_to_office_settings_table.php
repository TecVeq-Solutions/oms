<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            $table->integer('screenshot_interval_minutes')->default(10)->after('office_start_time');
            $table->integer('screenshot_compression_quality')->default(60)->after('screenshot_interval_minutes');
            $table->time('office_end_time')->nullable()->after('office_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            $table->dropColumn(['screenshot_interval_minutes', 'screenshot_compression_quality', 'office_end_time']);
        });
    }
};
