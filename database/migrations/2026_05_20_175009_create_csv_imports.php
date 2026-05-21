<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('csv_imports', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('file_name');
            $table->string('file_extension');
            $table->unsignedBigInteger('file_size');
            $table->string('file_path');
            $table->string('status')->default('pending');
            $table->unsignedInteger('total_rows')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csv_imports');
    }
};