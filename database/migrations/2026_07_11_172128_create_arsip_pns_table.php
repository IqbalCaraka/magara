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
        Schema::create('arsip_pns', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nip', 20)->nullable()->index();
            $table->string('nama', 100)->nullable();
            $table->string('instansi_kerja_id')->nullable()->index();
            $table->json('status_arsip')->nullable();
            $table->string('kategori_kelengkapan_2026', 50)->nullable();
            $table->decimal('skor_arsip_2026', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('instansi_kerja_id')->references('id')->on('instansi')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_pns');
    }
};
