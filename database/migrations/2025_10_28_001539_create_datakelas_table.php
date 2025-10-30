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
        Schema::create('datakelas', function (Blueprint $table) {

            $table->id('idkelas');
            $table->string('namakelas');
            $table->unsignedBigInteger('idwalas');
            $table->foreign('idwalas')->references('idwalas')->on('datawalas')->onDelete('cascade');
            $table->unsignedBigInteger('idsiswa');
            $table->foreign('idsiswa')->references('idsiswa')->on('datasiswa')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datakelas');
    }
};
