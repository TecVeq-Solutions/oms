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
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_tracked')->default(false)->after('status');
            $table->string('tracking_api_token')->unique()->nullable()->after('is_tracked');
            $table->timestamp('last_tracking_heartbeat')->nullable()->after('tracking_api_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['is_tracked', 'tracking_api_token', 'last_tracking_heartbeat']);
        });
    }
};
