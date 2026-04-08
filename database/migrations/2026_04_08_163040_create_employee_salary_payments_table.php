<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('employee_bank_accounts')->nullOnDelete();

            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salary_payments');
    }
};