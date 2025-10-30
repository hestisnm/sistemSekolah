<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dataguru', function (Blueprint $table) {
            $table->bigIncrements('idguru'); // ✅ primary key
            $table->string('nama');
            $table->string('mapel');
            
            // ✅ foreign key ke dataadmin
            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('dataadmin')->onDelete('cascade');
            
            $table->timestamps(); // ✅ cukup sekali
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataguru');
    }
};
