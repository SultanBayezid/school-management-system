<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 30)->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_type_id')->constrained()->restrictOnDelete();
            $table->string('month', 7)->nullable();   // YYYY-MM for monthly fees
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fine', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->date('issued_date');
            $table->string('status')->default('unpaid'); // unpaid | partial | paid
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'status']);
            $table->unique(['student_id', 'fee_type_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
