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
        Schema::table('users', function (Blueprint $table) {

            $table->string('image');
            $table->string('nomor_induk');
            $table->string('jurusan');
            $table->string('kelas');
            $table->foreignId('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->string('gender');
            $table->string('alamat');
            $table->string('nama_ortu');
            $table->string('alamat_ortu');
            $table->string('no_hp_ortu');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
