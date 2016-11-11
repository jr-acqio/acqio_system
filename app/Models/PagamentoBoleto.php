<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagamentoBoleto extends Model
{
  public $timestamps = false;
  protected $fillable = [
      'pagamento_id',
      'numero',
      'valor',
      'situacao',
      'data'
    ];
    protected $table = 'pagamentos_boleto';
}
