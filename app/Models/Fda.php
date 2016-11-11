<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fda extends Model
{
    // protected $primaryKey = 'fdaid';
    protected $table = "fdas";

    protected $fillable = [
      'fdaid',
      'documento',
      'nome_razao',
      'email',
      'endereco',
      'cep',
      'cidade',
      'uf'
    ];
    // Relacionamentos
    public function franqueados(){
      return $this->hasMany('App\Models\Franqueado','fdaid');
    }
}
