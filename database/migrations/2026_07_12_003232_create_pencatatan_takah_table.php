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
        Schema::create('pencatatan_takah', function (Blueprint $table) {
            $table->id();
            $table->string('nip');
            $table->string('kode_rak');
            $table->string('posisi_takah');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_created')->nullable();
            $table->boolean('status')->default(false)->comment('0=belum digenerate excel, 1=sudah');
            $table->boolean('is_different')->default(false)->comment('apakah berbeda dengan data DMS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencatatan_takah');
    }
};
