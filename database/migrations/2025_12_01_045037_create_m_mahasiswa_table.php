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
        Schema::create('m_mahasiswa', function (Blueprint $table) {
            $table->id('mahasiswa_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('prodi_id')->index();
            $table->unsignedBigInteger('kelas_id')->index();
            $table->string('mahasiswa_nama', 255);
            $table->string('mahasiswa_nim', 50);
            $table->string('mahasiswa_noHp', 20);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
            $table->foreign('kelas_id')->references('kelas_id')->on('m_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_mahasiswa');
    }
};
