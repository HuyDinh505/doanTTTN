<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Đổi collation cho toàn bộ bảng
        DB::statement('ALTER TABLE chi_tiet_dv CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        DB::statement('ALTER TABLE dich_vu_an_uong CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
    }

    public function down()
    {
        // Nếu muốn rollback về collation cũ (tùy chọn)
        DB::statement('ALTER TABLE chi_tiet_dv CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
        DB::statement('ALTER TABLE dich_vu_an_uong CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
    }
};
