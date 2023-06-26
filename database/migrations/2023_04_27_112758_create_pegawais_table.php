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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->string('id_pegawai')->primary();
            $table->string('id_role');
            $table->foreign('id_role')->references('id_role')->on('roles')->onDelete('cascade');
            $table->string('nama_pegawai');
            $table->string('jenis_kelamin');
            $table->string('alamat');
            $table->string('email');
            $table->string('password');
            $table->string('no_telp');
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
        Schema::dropIfExists('pegawais');
    }
};
