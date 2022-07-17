<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->string('nik_karyawan');
            $table->string('nama_karyawan');
            $table->date('tanggal_lahir');
            $table->enum('status_kawin', ['Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('alamat');
            $table->enum('gender', ['L', 'P']);
            $table->enum('pendidikan', ['SD/MI', 'SMP/MTs', 'SMA/MA', 'S1', 'S2', 'S3', 'Lainnya']);
            $table->string('agama');
            $table->string('telepon');
            $table->string('username');
            $table->string('password');
            $table->text('image');
            $table->enum('status_aktid', ['Aktif', 'Resign']);
            $table->integer('jumlah_anak');
            $table->enum('status_kerja', ['Tetap', 'Kontrak', 'Training', 'Evaluasi']);
            $table->integer('bulan_kontrak');
            $table->date('tanggal_mulai_kontrak');
            $table->date('tanggal_habis_kontrak');
            $table->date('tanggal_mulai_training');
            $table->date('tanggal_mulai_tetap');
            $table->date('tanggal_resign')->nullable();
            $table->string('bpjs_kesehatan');
            $table->string('bpjs_ketenagakerjaan');
            $table->timestamp('lastupdate_user');
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
        Schema::dropIfExists('karyawans');
    }
}
