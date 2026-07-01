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
        // Ubah 'activities_tab' menjadi 'activities'
        Schema::table('activities', function (Blueprint $table) {
            $table->string('realization_file')->nullable();
        });
    }

    public function down(): void
    {
        // Ubah 'activities_tab' menjadi 'activities'
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('realization_file');
        });
    }
};
