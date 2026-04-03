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
            $table->decimal('pph21', 15, 2)->nullable()->default(0)->after('pptk_id');
            $table->decimal('pph22', 15, 2)->nullable()->default(0)->after('pph21');
            $table->decimal('pph23', 15, 2)->nullable()->default(0)->after('pph22');
            $table->decimal('ppn', 15, 2)->nullable()->default(0)->after('pph23');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->dropColumn(['pph21', 'pph22', 'pph23', 'ppn']);
        });
    }
};
