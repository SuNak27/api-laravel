<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePangkatGolongansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pangkat_golongans', function (Blueprint $table) {
            $table->id('id_pangkat');
            $table->string('pangkat');
            $table->string('golongan');
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
        Schema::dropIfExists('pangkat_golongans');
    }
}