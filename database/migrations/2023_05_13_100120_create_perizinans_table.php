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
        Schema::create('perizinans', function (Blueprint $table) {
            $table->string('id_perizinan')->primary();
            $table->string('id_instruktur');
            $table->foreign('id_instruktur')->references('id_instruktur')->on('instrukturs')->onDelete('cascade');
            $table->string('id_jadwalH');
            $table->foreign('id_jadwalH')->references('id_jadwalH')->on('jadwal_harians')->onDelete('cascade');
            $table->string('id_instruktur_pengganti');
            $table->foreign('id_instruktur_pengganti')->references('id_instruktur')->on('instrukturs')->onDelete('cascade');
            $table->string('status');
            $table->string('keterangan');
            $table->date('tgl_izin');
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
        Schema::dropIfExists('perizinans');
    }
};
