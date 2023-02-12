<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_lunas', function (Blueprint $table) {
            $table->id();
            $table->string('noinv');
            $table->bigInteger('kurang');
            $table->bigInteger('bayar');
            $table->boolean('lunas');
            $table->boolean('aktif');
            $table->datetime('created_at');
            $table->string('created_by');
            $table->datetime('updated_at')->nullable();
            $table->string('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trans_lunas');
    }
};
