<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip_pns', function (Blueprint $table) {
            $table->index('nama');
            $table->index('kategori_kelengkapan_2026');
            $table->index('skor_arsip_2026');
        });

        Schema::table('instansi', function (Blueprint $table) {
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::table('arsip_pns', function (Blueprint $table) {
            $table->dropIndex(['nama']);
            $table->dropIndex(['kategori_kelengkapan_2026']);
            $table->dropIndex(['skor_arsip_2026']);
        });

        Schema::table('instansi', function (Blueprint $table) {
            $table->dropIndex(['nama']);
        });
    }
};
