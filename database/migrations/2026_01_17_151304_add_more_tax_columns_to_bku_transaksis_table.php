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
            $table->decimal('pajak_daerah', 15, 2)->nullable()->default(0)->after('ppn');
            $table->decimal('pph4_final', 15, 2)->nullable()->default(0)->after('pajak_daerah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->dropColumn(['pajak_daerah', 'pph4_final']);
        });
    }
};
