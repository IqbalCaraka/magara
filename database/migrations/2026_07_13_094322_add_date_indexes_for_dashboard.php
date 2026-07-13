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
        Schema::table('pencatatan_takah', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('date_created');
        });

        Schema::table('dokumen_scanning', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('date_created');
        });
    }

    public function down(): void
    {
        Schema::table('pencatatan_takah', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['date_created']);
        });

        Schema::table('dokumen_scanning', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['date_created']);
        });
    }
};
