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
        Schema::create('booking_gyms', function (Blueprint $table) {
            $table->string('id_booking_gym')->primary();
            $table->string('noStrukBG');
            $table->string('id_member');
            $table->foreign('id_member')->references('id_member')->on('members')->onDelete('cascade');
            $table->string('id_sesi');
            $table->foreign('id_sesi')->references('id_sesi')->on('sesi_gyms')->onDelete('cascade');
            $table->datetime('tgl_booking');
            $table->datetime('tgl_presensi')->nullable();
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
        Schema::dropIfExists('booking_gyms');
    }
};
