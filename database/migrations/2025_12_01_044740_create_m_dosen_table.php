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
        Schema::create('m_dosen', function (Blueprint $table) {
            $table->id('dosen_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('prodi_id')->index();
            $table->string('dosen_nama', 255);
            $table->string('dosen_nidn', 50);
            $table->string('dosen_noHp', 20);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_dosen');
    }
};
