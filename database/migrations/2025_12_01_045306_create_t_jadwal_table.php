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
        Schema::create('t_jadwal', function (Blueprint $table) {
            $table->id('jadwal_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('jadwal_nama', 255);
            $table->date('jadwal_tgl');
            $table->time('jadwal_jam_mulai');
            $table->time('jadwal_jam_selesai');
            $table->integer('jadwal_jumPes');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_jadwal');
    }
};
