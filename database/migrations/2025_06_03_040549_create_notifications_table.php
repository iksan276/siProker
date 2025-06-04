<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('NotificationID');
            $table->unsignedBigInteger('KegiatanID');
            $table->string('Title');
            $table->text('Description');
            $table->unsignedBigInteger('UserID'); // yang menerima notif
            $table->dateTime('read_at')->nullable();
            $table->dateTime('DCreated');
            $table->unsignedBigInteger('UCreated'); // yang mengirim notif
            
            $table->foreign('KegiatanID')->references('KegiatanID')->on('kegiatans');
            $table->foreign('UserID')->references('id')->on('users');
            $table->foreign('UCreated')->references('id')->on('users');
            
            $table->index(['UserID', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
