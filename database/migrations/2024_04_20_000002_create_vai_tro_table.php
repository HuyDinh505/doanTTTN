<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vai_tro', function (Blueprint $table) {
            $table->id('ma_vai_tro');
            $table->string('ten_vai_tro')->unique();
            $table->string('mo_ta')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vai_tro');
    }
};
