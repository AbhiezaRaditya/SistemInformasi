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
    
    \Illuminate\Support\Facades\DB::statement("
        ALTER TABLE activities 
        MODIFY COLUMN status ENUM('draft', 'pending', 'revisi', 'accept', 'reject', 'dalam_realisasi', 'completed')
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
