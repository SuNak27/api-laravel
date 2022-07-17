<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_karyawans', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_karyawan');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');
            $table->date('tangal');
            $table->foreignId('id_shift');
            $table->foreign('id_shift')->references('id_shift')->on('shifts');
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->timestamp('lastupdate_user')->default(now());
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('jadwal_karyawans');
    }
}
