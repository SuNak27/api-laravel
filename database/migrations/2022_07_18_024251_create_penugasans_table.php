<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenugasansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penugasans', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->date('tanggal_pengajuan');
            $table->foreignId('id_karyawan');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');
            $table->string('tujuan');
            $table->string('kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->integer('jumlah_menit');
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
        Schema::dropIfExists('penugasans');
    }
}
