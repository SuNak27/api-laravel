<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzinApprovedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('izin_approveds', function (Blueprint $table) {
            $table->id('id_detail_izin');
            $table->foreignId('id_izin_karyawan');
            $table->foreign('id_izin_karyawan')->references('id_izin_karyawan')->on('izin_karyawans');
            $table->foreignId('id_detail_jabatan');
            $table->foreign('id_detail_jabatan')->references('id_detail_jabatan')->on('detail_jabatans');
            $table->date('tanggal');
            $table->enum('status', ['Proses', 'Disetujui', 'Ditolak']);
            $table->text('keterangan_izin');
            $table->text('keterangan_acc');
            $table->integer('lastupdate_user')->default(1);
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
        Schema::dropIfExists('izin_approveds');
    }
}
