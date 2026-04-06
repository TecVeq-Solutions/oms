<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('office_settings', 'office_email')) {
                $table->string('office_email')->nullable()->after('office_name');
            }

            if (!Schema::hasColumn('office_settings', 'office_phone')) {
                $table->string('office_phone', 50)->nullable()->after('office_email');
            }

            if (!Schema::hasColumn('office_settings', 'office_address')) {
                $table->string('office_address', 500)->nullable()->after('office_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('office_settings', 'office_email')) {
                $columns[] = 'office_email';
            }

            if (Schema::hasColumn('office_settings', 'office_phone')) {
                $columns[] = 'office_phone';
            }

            if (Schema::hasColumn('office_settings', 'office_address')) {
                $columns[] = 'office_address';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};