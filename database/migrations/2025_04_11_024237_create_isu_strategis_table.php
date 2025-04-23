<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('isu_strategis', function (Blueprint $table) {
            $table->id('IsuID');
            $table->unsignedBigInteger('PilarID');
            $table->text('Nama');
            $table->enum('NA', ['Y', 'N'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
            $table->foreign('PilarID')->references('PilarID')->on('pilars');
            $table->foreign('UCreated')->references('id')->on('users');
            $table->foreign('UEdited')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isu_strategis');
    }
};
