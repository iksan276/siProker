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
            $table->text('IndikatorKinerjaID');
            $table->text('Nama');
            $table->text('Output');
            $table->text('Outcome');
            $table->unsignedBigInteger('JenisKegiatanID');
            $table->text('MataAnggaranID'); // Storing as comma-separated values
            $table->integer('JumlahKegiatan');
            $table->unsignedBigInteger('SatuanID');
            $table->integer('HargaSatuan');
            $table->integer('Total');
            $table->unsignedBigInteger('PenanggungJawabID'); // This will store the UnitID from API
            $table->text('PelaksanaID'); // Storing as comma-separated values of UnitIDs from API
            $table->enum('NA', ['Y', 'N'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
            $table->foreign('ProgramPengembanganID')->references('ProgramPengembanganID')->on('program_pengembangans');
            $table->foreign('JenisKegiatanID')->references('JenisKegiatanID')->on('jenis_kegiatans');
            $table->foreign('SatuanID')->references('SatuanID')->on('satuans');
            // No foreign key for PenanggungJawabID since it references an external API
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
