<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoyaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('royalties', function (Blueprint $table) {
            $table->increments('id');
            $table->date('data_vencimento');
            $table->text('cliente');
            $table->string('franquia_loc');
            $table->double('valor_original',8,2);
            $table->double('cheques_devolvidos',8,2);

            $table->integer('franqueadoid')->unsigned();
            $table->foreign('franqueadoid')->references('id')->on('franqueados')->onDelete('cascade');
            $table->string('descontado',1)->default('n');
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
        Schema::drop('royalties');
    }
}
