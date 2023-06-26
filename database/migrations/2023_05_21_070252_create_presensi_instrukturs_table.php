<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presensi_instrukturs', function (Blueprint $table) {
            $table->string('id_presensi_instruktur')->primary();
            $table->string('id_jadwalH');
            $table->foreign('id_jadwalH')->references('id_jadwalH')->on('jadwal_harians')->onDelete('cascade');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();
            $table->datetime('tgl_presensi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presensi_instrukturs');
    }
};
