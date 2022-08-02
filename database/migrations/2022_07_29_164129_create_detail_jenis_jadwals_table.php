<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailJenisJadwalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_jenis_jadwals', function (Blueprint $table) {
            $table->id('id_detail_jenis_jadwal');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->foreignId('id_jenis_jadwal');
            $table->foreign('id_jenis_jadwal')->references('id_jenis_jadwal')->on('jenis_jadwals');
            $table->foreignId('id_shift');
            $table->foreign('id_shift')->references('id_shift')->on('shifts');
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
        Schema::dropIfExists('detail_jenis_jadwals');
    }
}
