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
        Schema::create('m_tendik', function (Blueprint $table) {
            $table->id('tendik_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('tendik_nama', 255);
            $table->string('tendik_nidn', 50);
            $table->string('tendik_noHp', 20);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_tendik');
    }
};
