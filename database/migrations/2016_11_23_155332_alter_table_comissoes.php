<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableComissoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comissoes', function (Blueprint $table) {
          $table->integer('order_id')->unsigned()->nullable();
          $table->foreign('order_id')->references('id')->on('ordens_pagamento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints('pedidos', function ($table) {
          $table->dropForeign('order_id');
        });
    }
}
