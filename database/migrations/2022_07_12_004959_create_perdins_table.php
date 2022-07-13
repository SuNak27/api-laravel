<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerdinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perdins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan');
            $table->enum('status_perdin', ['Pengajuan', 'Diterima', 'Ditolak']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('kegiatan');
            $table->string('tempat_dinas');
            $table->date('tgl_pengajuan');
            $table->date('tgl_persetujuan')->nullable();
            $table->string('keterangan_persetujuan')->nullable();
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
        Schema::dropIfExists('perdins');
    }
}
