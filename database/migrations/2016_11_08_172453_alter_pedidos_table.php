<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('pedidos', function ($table) {
        $table->integer('franqueadoid')->after('cliente_id')->unsigned();
        $table->foreign('franqueadoid')->references('id')->on('franqueados')->onDelete('cascade');

        $table->integer('fdaid')->after('cliente_id')->unsigned();
        $table->foreign('fdaid')->references('id')->on('fdas')->onDelete('cascade');

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
          $table->dropForeign('franqueadoid');
          $table->dropForeign('fdaid');
        });
    }
}
