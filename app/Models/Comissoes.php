<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comissoes extends Model
{
    protected $table = 'comissoes';

    //Primary Key da Tabela.
    protected $primaryKey = 'id';
    public $timestamps = false;


    protected $fillable = [
      'data_cadastro',
      'nome_cliente',
      'cidade',
      'uf',
      'fdaid',
      'franqueadoid',
      'dispositivos',
      'quantidade',
      'serial',
      'nf'
    ];

    public function produtos(){
      // Parametros, tabela pivot, coluna do model corrente, coluna do model que deseja obter os dados;
      return $this->belongsToMany('App\Models\Produto','comissoes_produto','comissaoid','produtoid');
    }

    public function orders(){
      return $this->belongsToMany('App\Models\OrdemPagamento','comissoes_ordens_pagamento', 'idcomissao', 'idordempagamento');
    }
}
