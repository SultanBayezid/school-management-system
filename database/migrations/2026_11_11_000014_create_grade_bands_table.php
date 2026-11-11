<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_bands', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 5)->unique();      // A+, A, A-, B, C, D, F
            $table->decimal('min_percent', 5, 2);       // inclusive lower bound
            $table->decimal('max_percent', 5, 2);       // inclusive upper bound
            $table->decimal('grade_point', 3, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_bands');
    }
};
