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
        // 1. Buat tabel pivot study_program_user
        Schema::create('study_program_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'study_program_id']);
        });

        // 2. Buat tabel pivot unit_user
        Schema::create('unit_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'unit_id']);
        });

        // 3. Migrasi data lama dari kolom study_program_id & unit_id di tabel users
        //    ke tabel pivot yang baru, supaya data user yang sudah ada tidak hilang.
        $users = DB::table('users')
            ->select('id', 'study_program_id', 'unit_id')
            ->whereNotNull('study_program_id')
            ->orWhereNotNull('unit_id')
            ->get();

        foreach ($users as $user) {
            if (! is_null($user->study_program_id)) {
                DB::table('study_program_user')->insert([
                    'user_id' => $user->id,
                    'study_program_id' => $user->study_program_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (! is_null($user->unit_id)) {
                DB::table('unit_user')->insert([
                    'user_id' => $user->id,
                    'unit_id' => $user->unit_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. Hapus kolom lama dari tabel users (setelah data dipastikan sudah pindah)
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['study_program_id']);
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['study_program_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom lama
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('study_program_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('study_program_id')->constrained()->nullOnDelete();
        });

        // Kembalikan data (ambil salah satu / yang pertama saja, karena kolom lama hanya menampung 1 nilai)
        $pivotRows = DB::table('study_program_user')->get();
        foreach ($pivotRows as $row) {
            DB::table('users')->where('id', $row->user_id)->update([
                'study_program_id' => $row->study_program_id,
            ]);
        }

        $pivotRows = DB::table('unit_user')->get();
        foreach ($pivotRows as $row) {
            DB::table('users')->where('id', $row->user_id)->update([
                'unit_id' => $row->unit_id,
            ]);
        }

        Schema::dropIfExists('study_program_user');
        Schema::dropIfExists('unit_user');
    }
};