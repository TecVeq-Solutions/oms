<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_personal_details', function (Blueprint $table) {
            // Document verification statuses: null = not uploaded, pending = awaiting review, verified = approved, rejected = rejected
            $table->enum('cnic_front_status', ['pending', 'verified', 'rejected'])->nullable()->after('cnic_front_photo');
            $table->string('cnic_front_reject_reason')->nullable()->after('cnic_front_status');

            $table->enum('cnic_back_status', ['pending', 'verified', 'rejected'])->nullable()->after('cnic_back_photo');
            $table->string('cnic_back_reject_reason')->nullable()->after('cnic_back_status');

            $table->enum('document_1_status', ['pending', 'verified', 'rejected'])->nullable()->after('document_1');
            $table->string('document_1_reject_reason')->nullable()->after('document_1_status');

            $table->enum('document_2_status', ['pending', 'verified', 'rejected'])->nullable()->after('document_2');
            $table->string('document_2_reject_reason')->nullable()->after('document_2_status');

            $table->enum('document_3_status', ['pending', 'verified', 'rejected'])->nullable()->after('document_3');
            $table->string('document_3_reject_reason')->nullable()->after('document_3_status');
        });
    }

    public function down(): void
    {
        Schema::table('employee_personal_details', function (Blueprint $table) {
            $table->dropColumn([
                'cnic_front_status', 'cnic_front_reject_reason',
                'cnic_back_status',  'cnic_back_reject_reason',
                'document_1_status', 'document_1_reject_reason',
                'document_2_status', 'document_2_reject_reason',
                'document_3_status', 'document_3_reject_reason',
            ]);
        });
    }
};
