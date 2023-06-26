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
        Schema::create('transaksi_deposit_kelas', function (Blueprint $table) {
            $table->string('id_TdepoK')->primary();
            $table->string('no_strukK');
            $table->string('id_pegawai');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->onDelete('cascade');
            $table->string('id_member');
            $table->foreign('id_member')->references('id_member')->on('members')->onDelete('cascade');
            $table->string('id_promoK');
            $table->foreign('id_promoK')->references('id_promoK')->on('promo_kelas')->onDelete('cascade');
            $table->string('id_kelas');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->date('tgl_transaksi');
            $table->date('tgl_exp');
            $table->integer('depoK');
            $table->float('totalBayar', 10, 0);
            $table->integer('totalDepoK');
            $table->integer('bonus');
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
        Schema::dropIfExists('transaksi_deposit_kelas');
    }
};
