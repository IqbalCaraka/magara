<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip_pns', function (Blueprint $table) {
            $table->dropForeign(['instansi_kerja_id']);
        });
    }

    public function down(): void
    {
        Schema::table('arsip_pns', function (Blueprint $table) {
            $table->foreign('instansi_kerja_id')->references('id')->on('instansi')->nullOnDelete();
        });
    }
};
