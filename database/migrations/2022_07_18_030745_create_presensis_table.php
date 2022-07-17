<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresensisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id('id_presensi');
            $table->foreignId('id_karyawan');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');
            $table->foreignId('id_shift');
            $table->foreign('id_shift')->references('id_shift')->on('shifts');
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->enum('mode_absen', ['0', '1']);
            $table->text('keterangan')->nullable();
            $table->string('latitude');
            $table->string('longitude');
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
        Schema::dropIfExists('presensis');
    }
}
