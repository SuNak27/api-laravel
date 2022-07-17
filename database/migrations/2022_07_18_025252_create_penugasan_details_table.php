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
        Schema::dropIfExists('penugasan_details');
    }
}
