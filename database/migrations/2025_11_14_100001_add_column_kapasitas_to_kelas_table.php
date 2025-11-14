<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan kolom kapasitas ke tabel kelas jika belum ada
        if (!Schema::hasColumn('kelas', 'kapasitas')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->integer('kapasitas')->default(40)->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('kapasitas');
        });
    }
};
