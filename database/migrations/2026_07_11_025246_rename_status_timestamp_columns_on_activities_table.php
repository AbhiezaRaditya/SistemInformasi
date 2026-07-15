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
    Schema::table('activities', function (Blueprint $table) {
        $table->renameColumn('ditolak_at', 'reject_at');
        $table->renameColumn('selesai_at', 'completed_at');
    });
}

public function down(): void
{
    Schema::table('activities', function (Blueprint $table) {
        $table->renameColumn('reject_at', 'ditolak_at');
        $table->renameColumn('completed_at', 'selesai_at');
    });
}
};
