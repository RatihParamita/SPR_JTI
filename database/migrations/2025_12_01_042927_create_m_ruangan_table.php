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
        Schema::create('m_ruangan', function (Blueprint $table) {
            $table->id('ruangan_id');
            $table->string('ruangan_kode', 5)->unique();
            $table->string('ruangan_nama', 255);
            $table->string('ruangan_fasilitas', 255);
            $table->integer('ruangan_kuota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_ruangan');
    }
};
