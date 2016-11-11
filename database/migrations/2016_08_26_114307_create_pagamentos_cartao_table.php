<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagamentosCartaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos_cartao', function (Blueprint $table) {
          $table->increments('id');

          $table->integer('pagamento_id')->unsigned();
          $table->foreign('pagamento_id')->references('id')->on('pagamentos')->onDelete('cascade');

          $table->date('data');
          $table->time('hora');
          $table->string('codigo');
          $table->string('status');
          $table->string('nsu_acqio')->nullable();
          $table->string('nsu_adquirente')->nullable();
          $table->string('tipo')->nullable();
          $table->string('bandeira')->nullable();
          $table->integer('parcelas')->nullable();
          $table->string('tipo_parcelamento')->nullable();
          $table->string('moeda')->nullable();
          $table->float('valor_total');
          $table->float('valor_total_liquido')->nullable();
          $table->float('faturamento')->nullable();
          $table->string('origem')->nullable();
          $table->string('loja')->nullable();
          $table->string('documento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pagamentos_cartao');
    }
}
