<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quyen', function (Blueprint $table) {
            $table->id('ma_quyen');
            $table->string('ten_quyen')->unique();
            $table->string('mo_ta')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quyen');
    }
};
