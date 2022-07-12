<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan');
            $table->enum('jenis_izin', ['Izin', 'Cuti', 'Sakit']);
            $table->enum('status_izin', ['Pengajuan', 'Diterima', 'Ditolak']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tgl_pengajuan');
            $table->date('tgl_persetujuan')->nullable();
            $table->string('keterangan_izin')->nullable();
            $table->text('keterangan_persetujuan')->nullable();
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
        Schema::dropIfExists('izins');
    }
}
