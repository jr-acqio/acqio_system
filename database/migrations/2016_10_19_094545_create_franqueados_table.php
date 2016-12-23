<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFranqueadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franqueados', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('fdaid')->unsigned();
            $table->foreign('fdaid')->references('id')->on('fdas')->onDelete('cascade');

            $table->string('franqueadoid');

            $table->text('nome_razao')->nullable();
            $table->string('documento')->nullable();
            $table->string('email')->nullable();
            $table->text('endereco')->nullable();
            $table->string('cep')->nullable();
            $table->string('cidade');
            $table->string('uf',2);
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
        Schema::drop('franqueados');
    }
}
