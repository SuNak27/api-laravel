<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenugasanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penugasan_details', function (Blueprint $table) {
            $table->id('id_detail_penugasan');
            $table->foreignId('id_penugasan');
            $table->foreign('id_penugasan')->references('id_penugasan')->on('penugasans');
            $table->enum('status', ['Proses', 'Disetujui', 'Ditolak']);
            $table->text('keterangan_acc')->nullable();
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
        Schema::dropIfExists('penugasan_details');
    }
}
