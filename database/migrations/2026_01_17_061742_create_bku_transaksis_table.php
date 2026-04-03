<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bku_transaksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_bukti');
            $table->string('kode_rekening');
            $table->string('kode_sub_kegiatan')->nullable();
            $table->string('nama_sub_kegiatan')->nullable();
            $table->text('uraian');
            $table->string('penerima');
            $table->decimal('nominal', 15, 2);
            $table->boolean('status_cetak')->default(false);
            $table->string('qr_code_hash')->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bku_transaksis');
    }
};
