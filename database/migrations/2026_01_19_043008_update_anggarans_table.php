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
        Schema::table('anggarans', function (Blueprint $table) {
            $table->string('kode')->nullable()->after('id');
            $table->text('uraian')->nullable()->after('kode');
            $table->unsignedBigInteger('parent_id')->nullable()->after('pagu');
            $table->foreign('parent_id')->references('id')->on('anggarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggarans', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['kode', 'uraian', 'parent_id']);
        });
    }
};
