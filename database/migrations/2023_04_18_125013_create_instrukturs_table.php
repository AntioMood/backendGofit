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
        Schema::create('instrukturs', function (Blueprint $table) {

            $table->string('id_instruktur')->primary();
            $table->string('nama_instruktur');
            $table->string('jenis_kelamin');
            $table->date('tgl_lahir');
            $table->string('no_telp');
            $table->string('email');
            $table->string('pass');
            $table->time('jumlah_terlambat')->nullable();
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
        Schema::dropIfExists('instrukturs');
    }
};
