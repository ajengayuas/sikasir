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
        Schema::create('temp_kasirs', function (Blueprint $table) {
            $table->id();
            $table->biginteger('idproduk');
            $table->string('satuan');
            $table->biginteger('harga');
            $table->biginteger('hargabeli');
            $table->biginteger('qty');
            $table->biginteger('amount');
            $table->biginteger('amountbeli');
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
        Schema::dropIfExists('temp_kasirs');
    }
};
