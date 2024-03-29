<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdensPagamentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordens_pagamento', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fdaid')->unsigned()->nullable();
            $table->integer('franqueadoid')->unsigned()->nullable();

            $table->foreign('fdaid')->references('id')->on('fdas')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('franqueadoid')->references('id')->on('franqueados')
            ->onDelete('cascade')->onUpdate('cascade');


            $table->string('relatorio_pdf');
            $table->string('comprovante_pdf')->nullabe();
            $table->integer('mes_ref');
            $table->double('valor',8,2);
            $table->integer('status')->default(0);
            $table->timestamps();
        });

        Schema::create('comissoes_ordens_pagamento', function (Blueprint $table) {
            
            $table->integer('idcomissao')->unsigned();
            $table->integer('idordempagamento')->unsigned();

            $table->primary(['idcomissao','idordempagamento']);
            $table->foreign('idcomissao')->references('id')->on('comissoes')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('idordempagamento')->references('id')->on('ordens_pagamento')
            ->onDelete('cascade')->onUpdate('cascade');

            // $table->timestamps();
        });
        Schema::create('royalties_ordem_pagamentos', function (Blueprint $table) {
            $table->integer('idroyalties')->unsigned();
            $table->integer('idordempagamento')->unsigned();

            $table->primary(['idroyalties','idordempagamento']);

            $table->foreign('idroyalties')->references('id')->on('royalties')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('idordempagamento')->references('id')->on('ordens_pagamento')
            ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comissoes_ordens_pagamento');
        Schema::drop('royalties_ordem_pagamentos');
        Schema::drop('ordens_pagamento');
    }
}
