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
            $table->unsignedBigInteger('SatuanID');
            $table->text('Nama');
            $table->text('Baseline')->nullable();
            $table->year('Tahun1')->nullable();
            $table->year('Tahun2')->nullable();
            $table->year('Tahun3')->nullable();
            $table->year('Tahun4')->nullable();
            $table->enum('MendukungIKU', ['Y', 'N'])->default('Y');
            $table->enum('NA', ['Y', 'N'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
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
