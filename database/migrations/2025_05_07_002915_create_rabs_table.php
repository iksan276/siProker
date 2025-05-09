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
        Schema::create('rabs', function (Blueprint $table) {
            $table->id('RABID');
            $table->unsignedBigInteger('KegiatanID')->nullable();
            $table->unsignedBigInteger('SubKegiatanID')->nullable();
            $table->text('Komponen');
            $table->integer('Volume');
            $table->unsignedBigInteger('Satuan');
            $table->integer('HargaSatuan');
            $table->integer('Jumlah');
            $table->text('Feedback')->nullable();
            $table->enum('Status', ['N', 'Y', 'T', 'R'])->default('N');
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            $table->dateTime('DEdited')->nullable();
            $table->unsignedBigInteger('UEdited')->nullable();
            
            $table->foreign('KegiatanID')->references('KegiatanID')->on('kegiatans')->onDelete('cascade');
            $table->foreign('SubKegiatanID')->references('SubKegiatanID')->on('sub_kegiatans')->onDelete('cascade');
            $table->foreign('Satuan')->references('SatuanID')->on('satuans');
            $table->foreign('UCreated')->references('id')->on('users');
            $table->foreign('UEdited')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rabs');
    }
};
