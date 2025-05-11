<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'phim',
            'loai_phim',
            'phim_loai',
            'rap_phim',
            'phong_chieu',
            'ghe_ngoi',
            'suat_chieu',
            'loai_ve',
            'dat_ve',
            've_dat',
            'chi_tiet_ve',
            'dich_vu_an_uong',
            'chi_tiet_dv',
            'nguoi_dung',
            'vai_tro',
            'quyen',
            'vai_tro_quyen',
            'khuyen_mai',
            'personal_access_tokens',
            'sessions',
            'cache'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` ENGINE = InnoDB");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là thay đổi engine
    }
};
