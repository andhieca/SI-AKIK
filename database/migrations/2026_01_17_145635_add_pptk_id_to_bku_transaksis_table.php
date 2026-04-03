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
            $table->unsignedBigInteger('pptk_id')->nullable()->after('status_cetak');
            $table->foreign('pptk_id')->references('id')->on('pejabats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bku_transaksis', function (Blueprint $table) {
            $table->dropForeign(['pptk_id']);
            $table->dropColumn('pptk_id');
        });
    }
};
