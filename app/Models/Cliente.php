<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    public function pedidos(){
      return $this->hasMany('App\Models\Pedidos');
    }
}
