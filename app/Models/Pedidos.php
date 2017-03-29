<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    protected $fillable = ['status', 'data_cancel', 'motivo'];
    protected $table = 'pedidos';

    public function itens(){
      return $this->hasMany('App\Models\PedidosItens', 'pedido_id');
    }
    public function cliente(){
      return $this->belongsTo('App\Models\Cliente');
    }
    public function pagamentos(){
    	return $this->hasMany('App\Models\PedidosPagamentos','pedido_id');
    }
}
