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
      // Hapus constraint
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn(['school_id']);
});

// Perbaiki data jika perlu

// Tambahkan kembali constraint
Schema::table('users', function (Blueprint $table) {
    $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
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
