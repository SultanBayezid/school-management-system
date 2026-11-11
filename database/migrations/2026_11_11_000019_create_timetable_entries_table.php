<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday .. 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 40)->nullable();
            $table->timestamps();
            $table->unique(['school_class_id', 'section_id', 'day_of_week', 'start_time'], 'tt_class_slot_unique');
            $table->index(['teacher_id', 'day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
