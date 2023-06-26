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
        Schema::create('booking_kelas', function (Blueprint $table) {
            $table->string('id_booking')->primary();
            $table->string('noStrukBK');
            $table->string('id_member');
            $table->foreign('id_member')->references('id_member')->on('members')->onDelete('cascade');
            $table->string('id_jadwalH');
            $table->foreign('id_jadwalH')->references('id_jadwalH')->on('jadwal_harians')->onDelete('cascade');
            $table->datetime('tgl_booking');
            $table->boolean('status');
            $table->string('jenis_pembayaran');
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
        Schema::dropIfExists('booking_kelas');
    }
};
