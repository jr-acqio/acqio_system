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

}
