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
        Schema::create('transaksi_deposit_uangs', function (Blueprint $table) {
            $table->string('id_TdepoU')->primary();
            $table->string('no_strukU');
            $table->string('id_pegawai');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->onDelete('cascade');
            $table->string('id_member');
            $table->foreign('id_member')->references('id_member')->on('members')->onDelete('cascade');
            $table->string('id_promo');
            $table->foreign('id_promo')->references('id_promo')->on('promos')->onDelete('cascade');
            $table->date('tgl_transaksi');
            $table->float('depoU', 10, 0);
            $table->float('totalDepoU', 10, 0);
            $table->float('bonus', 10, 0);
            $table->float('sisa', 10, 0);
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
        Schema::dropIfExists('transaksi_deposit_uangs');
    }
};
