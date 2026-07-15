<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->timestamp('pending_at')->nullable()->after('updated_at');
            $table->timestamp('revisi_at')->nullable()->after('pending_at');
            $table->timestamp('ditolak_at')->nullable()->after('revisi_at');
            $table->timestamp('realisasi_at')->nullable()->after('ditolak_at');
            $table->timestamp('selesai_at')->nullable()->after('realisasi_at');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'pending_at',
                'revisi_at',
                'ditolak_at',
                'realisasi_at',
                'selesai_at',
            ]);
        });
    }
};