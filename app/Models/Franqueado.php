<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Franqueado extends Model
{
    // protected $primaryKey = 'franqueadoid';
    protected $table = "franqueados";

    protected $fillable = [
      'fdaid',
      'franqueadoid',
      'documento',
      'nome_razao',
      'email',
      'endereco',
      'cep',
      'cidade',
      'uf'
    ];

    public function fda(){
      return $this->hasOne('App\Models\Fda','id','fdaid');
    }

    public function hasRoyalties(){
      return $this->hasMany('App\Models\Royalties','id');
    }
}
