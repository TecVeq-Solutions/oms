<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_personal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('father_name')->nullable();
            $table->string('cnic_number')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();

            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            $table->string('profile_photo')->nullable();
            $table->string('cnic_front_photo')->nullable();
            $table->string('cnic_back_photo')->nullable();
            $table->string('document_1')->nullable();
            $table->string('document_2')->nullable();
            $table->string('document_3')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_personal_details');
    }
};
