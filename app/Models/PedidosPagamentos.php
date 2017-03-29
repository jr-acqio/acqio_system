<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidosPagamentos extends Model
{
    protected $table = 'pedidos_pagamentos';
    public $timestamps = false;

    protected $fillable = [
      'pagamento_id',
      'pedido_id'
    ];

    public function pagamento_cartoes(){
      return $this->hasMany('App\Models\Pagamento','id')->with('cartoes');
    }
    public function pagamento_boletos(){
      return $this->hasMany('App\Models\Pagamento','id')->with('boletos');
    }
}
