<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFdasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fdaid');
            // $table->primary('fdaid');

            $table->string('documento')->nullable();
            $table->text('nome_razao')->nullable();
            $table->string('email')->nullable();
            $table->text('endereco')->nullable();
            $table->string('cep')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf',2)->nullable();
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
        Schema::drop('fdas');
    }
}
