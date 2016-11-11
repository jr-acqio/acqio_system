<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagamentoCartao extends Model
{
  public $timestamps = false;
  protected $fillable = [
      'pagamento_id',
      'data',
      'hora',
      'codigo',
      'status',
      'nsu_acqio',
      'nsu_adquirente',
      'tipo',
      'bandeira',
      'parcelas',
      'tipo_parcelamento',
      'moeda',
      'valor_total',
      'valor_total_liquido',
      'faturamento',
      'origem',
      'loja',
      'documento'
    ];

    protected $table = 'pagamentos_cartao';
}
