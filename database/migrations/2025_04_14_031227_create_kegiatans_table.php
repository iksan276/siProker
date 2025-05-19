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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id('KegiatanID');
            $table->unsignedBigInteger('ProgramRektorID'); // Changed from IndikatorKinerjaID to ProgramRektorID
            $table->text('Nama');
            $table->dateTime('TanggalMulai');
            $table->dateTime('TanggalSelesai');
            $table->text('RincianKegiatan');
            $table->text('Feedback')->nullable();
            $table->enum('Status', ['N', 'Y', 'T', 'R', 'P', 'PT', 'YT', 'TT', 'RT'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
            $table->foreign('ProgramRektorID')->references('ProgramRektorID')->on('program_rektors');
            $table->foreign('UCreated')->references('id')->on('users');
            $table->foreign('UEdited')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
