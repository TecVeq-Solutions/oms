<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_title');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['employee_id', 'account_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_bank_accounts');
    }
};
