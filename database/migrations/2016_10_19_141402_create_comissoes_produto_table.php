<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComissoesProdutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissoes_produto', function (Blueprint $table) {
          $table->increments('id');

          $table->integer('comissaoid')->unsigned();
          $table->foreign('comissaoid')->references('id')->on('comissoes')->onDelete('cascade');

          $table->integer('produtoid')->unsigned();
          $table->foreign('produtoid')->references('id')->on('produtos')->onDelete('cascade');

          $table->float('tx_instalacao');
          $table->float('tx_venda');

          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comissoes_produto');
    }
}
