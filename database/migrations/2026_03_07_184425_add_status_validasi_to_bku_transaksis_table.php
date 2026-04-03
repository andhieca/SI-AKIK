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
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->boolean('status_validasi')->default(false)->after('status_cetak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->dropColumn('status_validasi');
        });
    }
};
