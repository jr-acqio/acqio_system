<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidosItens extends Model
{
    protected $table = 'pedidos_itens';

    public function pedido()
    {
        return $this->belongsTo('App\Models\Pedidos');
    }

    public function produto(){
      return $this->hasOne('App\Models\Produto','id','produto_id');
    }
}
