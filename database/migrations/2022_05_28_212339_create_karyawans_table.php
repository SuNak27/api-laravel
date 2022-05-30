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
            $table->id();
            $table->string('nik');
            $table->foreignId('id_jabatan');
            $table->foreignId('id_unit');
            $table->string('nama');
            $table->date('tanggal_lahir');
            $table->enum('status_kawin', ['Kawin', 'Belum Kawin']);
            $table->string('alamat');
            $table->enum('gender', ['L', 'P']);
            $table->enum('pendidikan', ['SMP Sederajat', 'SMA Sederajat', 'S1', 'S2', 'Lainnya']);
            $table->string('agama')->nullable();
            $table->string('telepon');
            $table->string('username')->default('user');
            $table->string('password')->default(bcrypt('user'));
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
