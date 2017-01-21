<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemPagamento extends Model
{
  protected $table = 'ordens_pagamento';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'relatorio_pdf', 'mes_ref', 'valor','status'
  ];


  public function comissoes(){
  	return $this->belongsToMany('App\Models\Comissoes','comissoes_ordens_pagamento','idordempagamento','idcomissao')->with('produtos');
  }

  public function fda(){
    return $this->belongsTo('App\Models\Fda','fdaid','id');
  }

  public function franqueado(){
   return $this->belongsTo('App\Models\Franqueado','franqueadoid','id'); 
  }

  public function royalties(){
    return $this->belongsToMany('App\Models\Royalties','royalties_ordem_pagamentos','idordempagamento','idroyalties');
  }
}
