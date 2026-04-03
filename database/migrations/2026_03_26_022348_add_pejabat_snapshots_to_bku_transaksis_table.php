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
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->string('nama_camat')->nullable();
            $table->string('nip_camat')->nullable();
            $table->string('nama_pptk')->nullable();
            $table->string('nip_pptk')->nullable();
            $table->string('nama_bendahara')->nullable();
            $table->string('nip_bendahara')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'nama_camat', 'nip_camat',
                'nama_pptk', 'nip_pptk',
                'nama_bendahara', 'nip_bendahara'
            ]);
        });
    }
};
