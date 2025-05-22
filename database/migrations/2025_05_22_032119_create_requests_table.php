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
        Schema::create('requests', function (Blueprint $table) {
            $table->id('RequestID');
            $table->unsignedBigInteger('KegiatanID')->nullable();
            $table->unsignedBigInteger('SubKegiatanID')->nullable();
            $table->unsignedBigInteger('RABID')->nullable();
            $table->text('Feedback')->nullable();
            $table->dateTime('DCreated')->nullable();
            $table->unsignedBigInteger('UCreated')->nullable();
            
            // Foreign keys
            $table->foreign('KegiatanID')->references('KegiatanID')->on('kegiatans')->onDelete('cascade');
            $table->foreign('SubKegiatanID')->references('SubKegiatanID')->on('sub_kegiatans')->onDelete('cascade');
            $table->foreign('RABID')->references('RABID')->on('rabs')->onDelete('cascade');
            $table->foreign('UCreated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
