<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();   // student login
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->nullOnDelete(); // parent login
            $table->string('admission_no', 30)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 40)->nullable();
            $table->text('address')->nullable();
            $table->string('blood_group', 8)->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 40)->nullable();
            $table->string('guardian_relation', 40)->nullable();
            $table->date('admission_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
