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
        Schema::create('program_rektors', function (Blueprint $table) {
            $table->id('ProgramRektorID');
            $table->unsignedBigInteger('ProgramPengembanganID');
            $table->unsignedBigInteger('IndikatorKinerjaID'); // Added IndikatorKinerjaID column
            $table->text('Nama');
            $table->text('Output');
            $table->text('Outcome');
            $table->unsignedBigInteger('JenisKegiatanID');
            $table->text('MataAnggaranID'); // Storing as comma-separated values
            $table->integer('JumlahKegiatan');
            $table->unsignedBigInteger('SatuanID');
            $table->integer('HargaSatuan');
            $table->integer('Total');
            $table->unsignedBigInteger('PenanggungJawabID');
            $table->text('PelaksanaID'); // Storing as comma-separated values
            $table->enum('NA', ['Y', 'N'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
            $table->foreign('ProgramPengembanganID')->references('ProgramPengembanganID')->on('program_pengembangans');
            $table->foreign('IndikatorKinerjaID')->references('IndikatorKinerjaID')->on('indikator_kinerjas'); // Added foreign key constraint
            $table->foreign('JenisKegiatanID')->references('JenisKegiatanID')->on('jenis_kegiatans');
            $table->foreign('SatuanID')->references('SatuanID')->on('satuans');
            $table->foreign('PenanggungJawabID')->references('UnitID')->on('units');
            $table->foreign('UCreated')->references('id')->on('users');
            $table->foreign('UEdited')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_rektors');
    }
};
