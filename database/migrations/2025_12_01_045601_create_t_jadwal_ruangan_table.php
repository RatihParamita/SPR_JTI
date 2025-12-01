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
        Schema::create('t_jadwal_ruangan', function (Blueprint $table) {
            $table->id('jadwal_ruangan_id');
            $table->unsignedBigInteger('jadwal_id')->index();
            $table->unsignedBigInteger('ruangan_id')->index();
            $table->timestamps();

            $table->foreign('jadwal_id')->references('jadwal_id')->on('t_jadwal');
            $table->foreign('ruangan_id')->references('ruangan_id')->on('m_ruangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_jadwal_ruangan');
    }
};
