<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComissoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comissoes', function (Blueprint $table) {
          $table->increments('id');
          $table->date('data_cadastro');
          $table->date('data_aprovacao');
          $table->string('nome_cliente');
          $table->string('cidade');
          $table->string('uf',2);

          $table->integer('fdaid')->unsigned();
          $table->foreign('fdaid')->references('id')->on('fdas')->onDelete('cascade');

          $table->integer('franqueadoid')->unsigned()->nullabe();
          $table->foreign('franqueadoid')->references('id')->on('franqueados')->onDelete('cascade');

          $table->string('serial');
          $table->string('nf')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comissoes');
    }
}
