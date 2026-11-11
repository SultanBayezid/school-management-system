<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('roll_no')->nullable();
            $table->string('status')->default('active'); // active | completed | dropped
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id']);
            $table->unique(['academic_year_id', 'school_class_id', 'section_id', 'roll_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
