<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('checkout_latitude', 10, 7)->nullable()->after('longitude');
            $table->decimal('checkout_longitude', 10, 7)->nullable()->after('checkout_latitude');
            $table->decimal('checkout_distance_from_office', 10, 2)->nullable()->after('distance_from_office');
            $table->string('checkout_photo_path')->nullable()->after('photo_path');
            $table->text('checkout_privacy_note')->nullable()->after('privacy_note');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'checkout_latitude',
                'checkout_longitude',
                'checkout_distance_from_office',
                'checkout_photo_path',
                'checkout_privacy_note',
            ]);
        });
    }
};
