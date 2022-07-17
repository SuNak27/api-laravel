<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailJabatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_jabatans', function (Blueprint $table) {
            $table->id('id_detail_jabatan');
            $table->foreignId('id_karyawan');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');
            $table->foreignId('id_jabatan');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatans');
            $table->foreignId('id_unit');
            $table->foreign('id_unit')->references('id_unit')->on('units');
            $table->foreignId('id_pangkat');
            $table->foreign('id_pangkat')->references('id_pangkat')->on('pangkat_golongans');
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
        Schema::dropIfExists('detail_jabatans');
    }
}
