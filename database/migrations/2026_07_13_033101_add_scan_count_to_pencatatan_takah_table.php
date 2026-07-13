<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pencatatan_takah', function (Blueprint $table) {
            $table->unsignedInteger('scan_count')->default(0)->after('status');
        });

        // Sync existing data
        DB::statement('
            UPDATE pencatatan_takah
            SET scan_count = (
                SELECT COUNT(*) FROM dokumen_scanning
                WHERE dokumen_scanning.nip = pencatatan_takah.nip
            )
        ');
    }

    public function down(): void
    {
        Schema::table('pencatatan_takah', function (Blueprint $table) {
            $table->dropColumn('scan_count');
        });
    }
};
