<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. Class Six
            $table->unsignedTinyInteger('level')->default(1); // sort order
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
