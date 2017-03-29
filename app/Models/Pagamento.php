<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'pagamentos';

    public function cartoes(){
      return $this->hasMany('App\Models\PagamentoCartao');
    }
    public function boletos(){
      return $this->hasMany('App\Models\PagamentoBoleto');
    }
}
