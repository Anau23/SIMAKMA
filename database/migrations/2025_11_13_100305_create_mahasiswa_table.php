<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('doswal_id')->constrained('dosens')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained()->onDelete('cascade');
            $table->year('angkatan');
            $table->integer('nim')->unique();
            $table->string('alamat');
            $table->string('no_telp');
            $table->enum('gender', ['L','P']);
            $table->string('religion');
            $table->year('tahun_akademik');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mahasiswas');
    }
};