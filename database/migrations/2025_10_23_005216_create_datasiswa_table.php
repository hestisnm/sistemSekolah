<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('datasiswa', function (Blueprint $table) {
            $table->id('idsiswa');
            $table->unsignedBigInteger('admin_id');
            $table->string('nama');
            $table->integer('tb');
            $table->float('bb');
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('dataadmin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasiswa');
    }
};
