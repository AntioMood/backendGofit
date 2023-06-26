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
        Schema::create('members', function (Blueprint $table) {
            $table->string('id_member')->primary();
            $table->string('nama_member');
            $table->date('tgl_lahir');
            $table->string('alamat');
            $table->string('email');
            $table->string('password');
            $table->string('no_telp');
            $table->float('deposit_uang', 10, 0);
            $table->boolean('status');
            $table->date('tgl_pembuatan')->nullable();
            $table->date('tgl_exp')->nullable();
            $table->string('jenis_kelamin');
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
        Schema::dropIfExists('members');
    }
};
