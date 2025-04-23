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
        Schema::create('indikator_kinerjas', function (Blueprint $table) {
            $table->id('IndikatorKinerjaID');
            $table->unsignedBigInteger('ProgramRektorID');
            $table->unsignedBigInteger('SatuanID');
            $table->text('Nama');
            $table->integer('Bobot');
            $table->integer('HargaSatuan');
            $table->integer('Jumlah');
            $table->text('MetaAnggaranID'); // Comma-separated IDs
            $table->text('UnitTerkaitID');
            $table->enum('NA', ['Y', 'N'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
            $table->foreign('ProgramRektorID')->references('ProgramRektorID')->on('program_rektors');
            $table->foreign('SatuanID')->references('SatuanID')->on('satuans');
            $table->foreign('UCreated')->references('id')->on('users');
            $table->foreign('UEdited')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_kinerjas');
    }
};
