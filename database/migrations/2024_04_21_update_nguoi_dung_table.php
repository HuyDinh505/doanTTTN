<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('vai_tro');
            $table->foreignId('ma_vai_tro')->nullable()->constrained('vai_tro', 'ma_vai_tro');
        });
    }

    public function down()
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('vai_tro');
            $table->dropForeign(['ma_vai_tro']);
            $table->dropColumn('ma_vai_tro');
        });
    }
};
