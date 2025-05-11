<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cải thiện bảng dat_ve
        Schema::table('dat_ve', function (Blueprint $table) {
            // Thêm index cho ngay_dat
            $table->index('ngay_dat_ve', 'idx_ngay_dat');

            // Thêm ràng buộc check cho tong_gia_tien và tong_so_ve
            DB::statement('ALTER TABLE dat_ve ADD CONSTRAINT check_tong_gia_tien CHECK (tong_gia_tien > 0)');
            DB::statement('ALTER TABLE dat_ve ADD CONSTRAINT check_tong_so_ve CHECK (tong_so_ve > 0)');

            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'dat_ve'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND (CONSTRAINT_NAME LIKE '%ma_suat_chieu%' OR CONSTRAINT_NAME LIKE '%ma_nguoi_dung%')
            ");

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }

            // Cho phép ma_nguoi_dung có thể NULL
            $table->integer('ma_nguoi_dung')->nullable()->change();

            $table->foreign('ma_suat_chieu')
                ->references('ma_suat_chieu')
                ->on('suat_chieu')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // Cải thiện bảng ve_dat
        Schema::table('ve_dat', function (Blueprint $table) {
            // Thêm ràng buộc check cho so_luong và gia_tien
            DB::statement('ALTER TABLE ve_dat ADD CONSTRAINT check_so_luong CHECK (so_luong > 0)');
            DB::statement('ALTER TABLE ve_dat ADD CONSTRAINT check_gia_tien CHECK (gia_tien > 0)');

            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 've_dat'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND (CONSTRAINT_NAME LIKE '%ma_ve%' OR CONSTRAINT_NAME LIKE '%ma_loai_ve%')
            ");

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }

            $table->foreign('ma_ve')
                ->references('ma_ve')
                ->on('dat_ve')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('ma_loai_ve')
                ->references('ma_loai_ve')
                ->on('loai_ve')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // Cải thiện bảng chi_tiet_ve
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'chi_tiet_ve'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND (CONSTRAINT_NAME LIKE '%ma_ve%' OR CONSTRAINT_NAME LIKE '%ma_ghe%')
            ");

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }

            $table->foreign('ma_ve')
                ->references('ma_ve')
                ->on('dat_ve')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('ma_ghe')
                ->references('ma_ghe')
                ->on('ghe_ngoi')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // Cải thiện bảng loai_ve
        Schema::table('loai_ve', function (Blueprint $table) {
            // Thêm ràng buộc check cho gia_ve
            DB::statement('ALTER TABLE loai_ve ADD CONSTRAINT check_gia_ve CHECK (gia_ve > 0)');
        });

        // Cải thiện bảng chi_tiet_dv
        Schema::table('chi_tiet_dv', function (Blueprint $table) {
            // Thêm ràng buộc check cho so_luong và tong_gia_tien
            DB::statement('ALTER TABLE chi_tiet_dv ADD CONSTRAINT check_so_luong_dv CHECK (so_luong > 0)');
            DB::statement('ALTER TABLE chi_tiet_dv ADD CONSTRAINT check_tong_gia_tien_dv CHECK (tong_gia_tien > 0)');

            // Thêm các trường mới
            $table->timestamp('ngay_tao')->useCurrent()->comment('Thời gian tạo chi tiết dịch vụ');
            $table->enum('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'da_huy'])->default('cho_xac_nhan')->comment('Trạng thái của chi tiết dịch vụ');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú về chi tiết dịch vụ');

            // Thêm index
            $table->index('ngay_tao', 'idx_ngay_tao_dv');
            $table->index('trang_thai', 'idx_trang_thai_ctdv');
            $table->index(['ma_ve', 'ma_dv_an_uong'], 'idx_ma_ve_dv');

            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'chi_tiet_dv'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND (CONSTRAINT_NAME LIKE '%ma_ve%' OR CONSTRAINT_NAME LIKE '%ma_dv_an_uong%')
            ");

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }

            $table->foreign('ma_ve')
                ->references('ma_ve')
                ->on('dat_ve')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('ma_dv_an_uong')
                ->references('ma_dv_an_uong')
                ->on('dich_vu_an_uong')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // Cải thiện bảng dich_vu_an_uong
        Schema::table('dich_vu_an_uong', function (Blueprint $table) {
            // Thêm ràng buộc check cho gia_tien
            DB::statement('ALTER TABLE dich_vu_an_uong ADD CONSTRAINT check_gia_tien_dv CHECK (gia_tien > 0)');

            // Thêm các trường mới
            $table->integer('so_luong_toi_da')->default(100)->comment('Số lượng tối đa có thể đặt');
            $table->boolean('trang_thai')->default(true)->comment('true: đang hoạt động, false: ngừng hoạt động');
            $table->text('mo_ta')->nullable()->comment('Mô tả chi tiết về dịch vụ');
            $table->string('don_vi_tinh', 20)->default('cái')->comment('Đơn vị tính của dịch vụ');

            // Thêm index
            $table->index('trang_thai', 'idx_trang_thai_dv');
            $table->index('loai', 'idx_loai_dv');
        });

        // Thêm trigger để kiểm tra tong_so_ve và tong_gia_tien
        DB::unprepared('
            CREATE TRIGGER check_tong_so_ve_insert
            AFTER INSERT ON ve_dat
            FOR EACH ROW
            BEGIN
                DECLARE total_so_luong INT;
                DECLARE total_gia_tien DECIMAL(10,2);

                -- Tính tổng số lượng vé
                SELECT SUM(so_luong) INTO total_so_luong
                FROM ve_dat
                WHERE ma_ve = NEW.ma_ve;

                -- Tính tổng giá tiền
                SELECT SUM(so_luong * gia_tien) INTO total_gia_tien
                FROM ve_dat
                WHERE ma_ve = NEW.ma_ve;

                -- Cập nhật dat_ve
                UPDATE dat_ve
                SET tong_so_ve = total_so_luong,
                    tong_gia_tien = total_gia_tien
                WHERE ma_ve = NEW.ma_ve;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_tong_so_ve_update
            AFTER UPDATE ON ve_dat
            FOR EACH ROW
            BEGIN
                DECLARE total_so_luong INT;
                DECLARE total_gia_tien DECIMAL(10,2);

                -- Tính tổng số lượng vé
                SELECT SUM(so_luong) INTO total_so_luong
                FROM ve_dat
                WHERE ma_ve = NEW.ma_ve;

                -- Tính tổng giá tiền
                SELECT SUM(so_luong * gia_tien) INTO total_gia_tien
                FROM ve_dat
                WHERE ma_ve = NEW.ma_ve;

                -- Cập nhật dat_ve
                UPDATE dat_ve
                SET tong_so_ve = total_so_luong,
                    tong_gia_tien = total_gia_tien
                WHERE ma_ve = NEW.ma_ve;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_tong_so_ve_delete
            AFTER DELETE ON ve_dat
            FOR EACH ROW
            BEGIN
                DECLARE total_so_luong INT;
                DECLARE total_gia_tien DECIMAL(10,2);

                -- Tính tổng số lượng vé
                SELECT SUM(so_luong) INTO total_so_luong
                FROM ve_dat
                WHERE ma_ve = OLD.ma_ve;

                -- Tính tổng giá tiền
                SELECT SUM(so_luong * gia_tien) INTO total_gia_tien
                FROM ve_dat
                WHERE ma_ve = OLD.ma_ve;

                -- Cập nhật dat_ve
                UPDATE dat_ve
                SET tong_so_ve = total_so_luong,
                    tong_gia_tien = total_gia_tien
                WHERE ma_ve = OLD.ma_ve;
            END;
        ');

        // Thêm trigger để kiểm tra số lượng ghế
        DB::unprepared('
            CREATE TRIGGER check_so_luong_ghe_insert
            AFTER INSERT ON chi_tiet_ve
            FOR EACH ROW
            BEGIN
                DECLARE total_so_luong INT;
                DECLARE so_luong_ghe INT;

                -- Tính tổng số lượng vé
                SELECT SUM(so_luong) INTO total_so_luong
                FROM ve_dat
                WHERE ma_ve = NEW.ma_ve;

                -- Tính số lượng ghế
                SELECT COUNT(*) INTO so_luong_ghe
                FROM chi_tiet_ve
                WHERE ma_ve = NEW.ma_ve;

                -- Kiểm tra tính nhất quán
                IF total_so_luong != so_luong_ghe THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Số lượng vé không khớp với số lượng ghế\';
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_so_luong_ghe_update
            AFTER UPDATE ON chi_tiet_ve
            FOR EACH ROW
            BEGIN
                DECLARE total_so_luong INT;
                DECLARE so_luong_ghe INT;

                -- Tính tổng số lượng vé
                SELECT SUM(so_luong) INTO total_so_luong
                FROM ve_dat
                WHERE ma_ve = NEW.ma_ve;

                -- Tính số lượng ghế
                SELECT COUNT(*) INTO so_luong_ghe
                FROM chi_tiet_ve
                WHERE ma_ve = NEW.ma_ve;

                -- Kiểm tra tính nhất quán
                IF total_so_luong != so_luong_ghe THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Số lượng vé không khớp với số lượng ghế\';
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_so_luong_ghe_delete
            AFTER DELETE ON chi_tiet_ve
            FOR EACH ROW
            BEGIN
                DECLARE total_so_luong INT;
                DECLARE so_luong_ghe INT;

                -- Tính tổng số lượng vé
                SELECT SUM(so_luong) INTO total_so_luong
                FROM ve_dat
                WHERE ma_ve = OLD.ma_ve;

                -- Tính số lượng ghế
                SELECT COUNT(*) INTO so_luong_ghe
                FROM chi_tiet_ve
                WHERE ma_ve = OLD.ma_ve;

                -- Kiểm tra tính nhất quán
                IF total_so_luong != so_luong_ghe THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Số lượng vé không khớp với số lượng ghế\';
                END IF;
            END;
        ');

        // Thêm trigger để kiểm tra chi tiết dịch vụ
        DB::unprepared('
            CREATE TRIGGER check_chi_tiet_dv_insert
            BEFORE INSERT ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE gia_tien_dv DECIMAL(10,2);

                -- Lấy giá tiền của dịch vụ
                SELECT gia_tien INTO gia_tien_dv
                FROM dich_vu_an_uong
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong;

                -- Kiểm tra tong_gia_tien
                IF NEW.tong_gia_tien != NEW.so_luong * gia_tien_dv THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Tổng giá tiền không khớp với số lượng và giá dịch vụ\';
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_chi_tiet_dv_update
            BEFORE UPDATE ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE gia_tien_dv DECIMAL(10,2);

                -- Lấy giá tiền của dịch vụ
                SELECT gia_tien INTO gia_tien_dv
                FROM dich_vu_an_uong
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong;

                -- Kiểm tra tong_gia_tien
                IF NEW.tong_gia_tien != NEW.so_luong * gia_tien_dv THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Tổng giá tiền không khớp với số lượng và giá dịch vụ\';
                END IF;
            END;
        ');

        // Thêm trigger để cập nhật tong_gia_tien trong dat_ve
        DB::unprepared('
            CREATE TRIGGER update_tong_gia_tien_dv_insert
            AFTER INSERT ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE total_gia_tien DECIMAL(10,2);

                -- Tính tổng giá tiền từ ve_dat và chi_tiet_dv
                SELECT
                    COALESCE(SUM(vd.so_luong * vd.gia_tien), 0) +
                    COALESCE(SUM(ctdv.tong_gia_tien), 0)
                INTO total_gia_tien
                FROM dat_ve dv
                LEFT JOIN ve_dat vd ON dv.ma_ve = vd.ma_ve
                LEFT JOIN chi_tiet_dv ctdv ON dv.ma_ve = ctdv.ma_ve
                WHERE dv.ma_ve = NEW.ma_ve;

                -- Cập nhật tong_gia_tien trong dat_ve
                UPDATE dat_ve
                SET tong_gia_tien = total_gia_tien
                WHERE ma_ve = NEW.ma_ve;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER update_tong_gia_tien_dv_update
            AFTER UPDATE ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE total_gia_tien DECIMAL(10,2);

                -- Tính tổng giá tiền từ ve_dat và chi_tiet_dv
                SELECT
                    COALESCE(SUM(vd.so_luong * vd.gia_tien), 0) +
                    COALESCE(SUM(ctdv.tong_gia_tien), 0)
                INTO total_gia_tien
                FROM dat_ve dv
                LEFT JOIN ve_dat vd ON dv.ma_ve = vd.ma_ve
                LEFT JOIN chi_tiet_dv ctdv ON dv.ma_ve = ctdv.ma_ve
                WHERE dv.ma_ve = NEW.ma_ve;

                -- Cập nhật tong_gia_tien trong dat_ve
                UPDATE dat_ve
                SET tong_gia_tien = total_gia_tien
                WHERE ma_ve = NEW.ma_ve;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER update_tong_gia_tien_dv_delete
            AFTER DELETE ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE total_gia_tien DECIMAL(10,2);

                -- Tính tổng giá tiền từ ve_dat và chi_tiet_dv
                SELECT
                    COALESCE(SUM(vd.so_luong * vd.gia_tien), 0) +
                    COALESCE(SUM(ctdv.tong_gia_tien), 0)
                INTO total_gia_tien
                FROM dat_ve dv
                LEFT JOIN ve_dat vd ON dv.ma_ve = vd.ma_ve
                LEFT JOIN chi_tiet_dv ctdv ON dv.ma_ve = ctdv.ma_ve
                WHERE dv.ma_ve = OLD.ma_ve;

                -- Cập nhật tong_gia_tien trong dat_ve
                UPDATE dat_ve
                SET tong_gia_tien = total_gia_tien
                WHERE ma_ve = OLD.ma_ve;
            END;
        ');

        // Thêm trigger để kiểm tra số lượng tối đa
        DB::unprepared('
            CREATE TRIGGER check_so_luong_toi_da_insert
            BEFORE INSERT ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE so_luong_toi_da INT;
                DECLARE tong_so_luong INT;

                -- Lấy số lượng tối đa của dịch vụ
                SELECT so_luong_toi_da INTO so_luong_toi_da
                FROM dich_vu_an_uong
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong;

                -- Tính tổng số lượng đã đặt
                SELECT COALESCE(SUM(so_luong), 0) INTO tong_so_luong
                FROM chi_tiet_dv
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong
                AND trang_thai != \'da_huy\';

                -- Kiểm tra số lượng
                IF tong_so_luong + NEW.so_luong > so_luong_toi_da THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Số lượng vượt quá giới hạn cho phép\';
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_so_luong_toi_da_update
            BEFORE UPDATE ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE so_luong_toi_da INT;
                DECLARE tong_so_luong INT;

                -- Lấy số lượng tối đa của dịch vụ
                SELECT so_luong_toi_da INTO so_luong_toi_da
                FROM dich_vu_an_uong
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong;

                -- Tính tổng số lượng đã đặt (trừ số lượng cũ)
                SELECT COALESCE(SUM(so_luong), 0) INTO tong_so_luong
                FROM chi_tiet_dv
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong
                AND trang_thai != \'da_huy\'
                AND (ma_ve != NEW.ma_ve OR ma_dv_an_uong != NEW.ma_dv_an_uong);

                -- Kiểm tra số lượng
                IF tong_so_luong + NEW.so_luong > so_luong_toi_da THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Số lượng vượt quá giới hạn cho phép\';
                END IF;
            END;
        ');

        // Thêm trigger để kiểm tra trạng thái dịch vụ
        DB::unprepared('
            CREATE TRIGGER check_trang_thai_dv_insert
            BEFORE INSERT ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE trang_thai_dv BOOLEAN;

                -- Lấy trạng thái của dịch vụ
                SELECT trang_thai INTO trang_thai_dv
                FROM dich_vu_an_uong
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong;

                -- Kiểm tra trạng thái
                IF NOT trang_thai_dv THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Dịch vụ đã ngừng hoạt động\';
                END IF;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER check_trang_thai_dv_update
            BEFORE UPDATE ON chi_tiet_dv
            FOR EACH ROW
            BEGIN
                DECLARE trang_thai_dv BOOLEAN;

                -- Lấy trạng thái của dịch vụ
                SELECT trang_thai INTO trang_thai_dv
                FROM dich_vu_an_uong
                WHERE ma_dv_an_uong = NEW.ma_dv_an_uong;

                -- Kiểm tra trạng thái
                IF NOT trang_thai_dv THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Dịch vụ đã ngừng hoạt động\';
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa các trigger
        DB::unprepared('DROP TRIGGER IF EXISTS check_tong_so_ve_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_tong_so_ve_update');
        DB::unprepared('DROP TRIGGER IF EXISTS check_tong_so_ve_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS check_so_luong_ghe_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_so_luong_ghe_update');
        DB::unprepared('DROP TRIGGER IF EXISTS check_so_luong_ghe_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS check_chi_tiet_dv_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_chi_tiet_dv_update');
        DB::unprepared('DROP TRIGGER IF EXISTS update_tong_gia_tien_dv_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS update_tong_gia_tien_dv_update');
        DB::unprepared('DROP TRIGGER IF EXISTS update_tong_gia_tien_dv_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS check_so_luong_toi_da_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_so_luong_toi_da_update');
        DB::unprepared('DROP TRIGGER IF EXISTS check_trang_thai_dv_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_trang_thai_dv_update');

        // Xóa các cải thiện của bảng dat_ve
        Schema::table('dat_ve', function (Blueprint $table) {
            $table->dropIndex('idx_ngay_dat');
            DB::statement('ALTER TABLE dat_ve DROP CONSTRAINT check_tong_gia_tien');
            DB::statement('ALTER TABLE dat_ve DROP CONSTRAINT check_tong_so_ve');
            $table->dropForeign(['ma_suat_chieu']);
            $table->dropForeign(['ma_nguoi_dung']);
            $table->integer('ma_nguoi_dung')->nullable(false)->change();
            $table->foreign('ma_suat_chieu')
                ->references('ma_suat_chieu')
                ->on('suat_chieu')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // Xóa các cải thiện của bảng ve_dat
        Schema::table('ve_dat', function (Blueprint $table) {
            DB::statement('ALTER TABLE ve_dat DROP CONSTRAINT check_so_luong');
            DB::statement('ALTER TABLE ve_dat DROP CONSTRAINT check_gia_tien');
            $table->dropForeign(['ma_ve']);
            $table->dropForeign(['ma_loai_ve']);
            $table->foreign('ma_ve')
                ->references('ma_ve')
                ->on('dat_ve')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('ma_loai_ve')
                ->references('ma_loai_ve')
                ->on('loai_ve')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        // Xóa các cải thiện của bảng chi_tiet_ve
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            $table->dropForeign(['ma_ve']);
            $table->dropForeign(['ma_ghe']);
            $table->foreign('ma_ve')
                ->references('ma_ve')
                ->on('dat_ve')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign('ma_ghe')
                ->references('ma_ghe')
                ->on('ghe_ngoi')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // Xóa các cải thiện của bảng loai_ve
        Schema::table('loai_ve', function (Blueprint $table) {
            DB::statement('ALTER TABLE loai_ve DROP CONSTRAINT check_gia_ve');
        });

        // Xóa các cải thiện của bảng chi_tiet_dv
        Schema::table('chi_tiet_dv', function (Blueprint $table) {
            $table->dropIndex('idx_ngay_tao_dv');
            $table->dropIndex('idx_trang_thai_ctdv');
            $table->dropIndex('idx_ma_ve_dv');
            $table->dropColumn(['ngay_tao', 'trang_thai', 'ghi_chu']);
            DB::statement('ALTER TABLE chi_tiet_dv DROP CONSTRAINT check_so_luong_dv');
            DB::statement('ALTER TABLE chi_tiet_dv DROP CONSTRAINT check_tong_gia_tien_dv');
            $table->dropForeign(['ma_ve']);
            $table->dropForeign(['ma_dv_an_uong']);
            $table->foreign('ma_ve')
                ->references('ma_ve')
                ->on('dat_ve')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('ma_dv_an_uong')
                ->references('ma_dv_an_uong')
                ->on('dich_vu_an_uong')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        // Xóa các cải thiện của bảng dich_vu_an_uong
        Schema::table('dich_vu_an_uong', function (Blueprint $table) {
            $table->dropIndex('idx_trang_thai_dv');
            $table->dropIndex('idx_loai_dv');
            $table->dropColumn(['so_luong_toi_da', 'trang_thai', 'mo_ta', 'don_vi_tinh']);
            DB::statement('ALTER TABLE dich_vu_an_uong DROP CONSTRAINT check_gia_tien_dv');
        });
    }
};
