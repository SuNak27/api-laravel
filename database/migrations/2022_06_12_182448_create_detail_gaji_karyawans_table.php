<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailGajiKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_gaji_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_detail_gaji");
            $table->foreignId("id_karyawan");
            $table->string("bulan");
            $table->foreignId("denda");
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
        Schema::dropIfExists('detail_gaji_karyawans');
    }
}
