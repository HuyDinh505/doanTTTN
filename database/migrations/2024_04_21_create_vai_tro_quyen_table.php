<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vai_tro_quyen', function (Blueprint $table) {
            $table->integer('ma_vai_tro_quyen', true);
            $table->unsignedBigInteger('ma_vai_tro');
            $table->unsignedBigInteger('ma_quyen');

            // Thêm foreign key constraints
            $table->foreign('ma_vai_tro')
                ->references('ma_vai_tro')
                ->on('vai_tro')
                ->onDelete('cascade');

            $table->foreign('ma_quyen')
                ->references('ma_quyen')
                ->on('quyen')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vai_tro_quyen');
    }
};
