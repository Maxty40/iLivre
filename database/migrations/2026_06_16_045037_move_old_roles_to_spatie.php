<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasikan data role_id lama dari tabel users
     * ke tabel model_has_roles milik Spatie,
     * lalu hapus kolom role_id dari tabel users.
     */
    public function up(): void
    {
        // 1. Ambil semua user yang masih punya role_id
        $users = DB::table('users')
            ->whereNotNull('role_id')
            ->select('id', 'role_id')
            ->get();

        // 2. Insert ke model_has_roles (upsert agar idempoten)
        foreach ($users as $user) {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id'    => $user->role_id,
                'model_type' => 'App\\Models\\User',
                'model_id'   => $user->id,
            ]);
        }

        // 3. Setelah data berhasil dimigrasikan, hapus kolom role_id
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });
    }

    /**
     * Kembalikan kolom role_id jika migrasi di-rollback.
     * Data tidak bisa dikembalikan secara otomatis.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
        });
    }
};
