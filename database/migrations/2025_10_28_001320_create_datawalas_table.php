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
        Schema::create('datawalas', function (Blueprint $table) {
            $table->id('idwalas');
            $table->string('jenjang');
            $table->string('namakelas');
            $table->string('tahunajaran');
            $table->unsignedBigInteger('idguru')->unique();
            $table->foreign('idguru')->references('idguru')->on('dataguru')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datawalas');
    }
};
