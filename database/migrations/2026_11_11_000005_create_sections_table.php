<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->string('name', 30);      // e.g. Section A
            $table->unsignedSmallInteger('capacity')->default(40);
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete(); // class teacher
            $table->timestamps();
            $table->unique(['school_class_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
