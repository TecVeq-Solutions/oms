<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            if (
                Schema::hasColumn('office_settings', 'office_laititude') &&
                !Schema::hasColumn('office_settings', 'office_latitude')
            ) {
                $table->renameColumn('office_laititude', 'office_latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            if (
                Schema::hasColumn('office_settings', 'office_latitude') &&
                !Schema::hasColumn('office_settings', 'office_laititude')
            ) {
                $table->renameColumn('office_latitude', 'office_laititude');
            }
        });
    }
};