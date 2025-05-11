<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Lấy danh sách tất cả các bảng
    $tables = DB::select('SHOW TABLES');
    $databaseName = config('database.connections.mysql.database');
    $tables = array_map(function($table) use ($databaseName) {
        return $table->{'Tables_in_'.$databaseName};
    }, $tables);

    foreach ($tables as $table) {
        try {
            // Kiểm tra engine hiện tại
            $tableInfo = DB::select("SHOW TABLE STATUS WHERE Name = ?", [$table])[0];

            if ($tableInfo->Engine !== 'InnoDB') {
                // Chuyển đổi sang InnoDB
                DB::statement("ALTER TABLE `$table` ENGINE = InnoDB");
                echo "Converted table $table to InnoDB successfully\n";
            } else {
                echo "Table $table is already using InnoDB\n";
            }
        } catch (Exception $e) {
            echo "Error converting table $table: " . $e->getMessage() . "\n";
        }
    }

    echo "\nAll tables have been processed.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
