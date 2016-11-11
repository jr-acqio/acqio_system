<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    protected $table = 'pedidos';

    public function produtos(){
      return $this->hasMany('App\Models\PedidosItens', 'pedido_id');
    }
    public function cliente(){
      return $this->belongsTo('App\Models\Cliente');
    }
}
