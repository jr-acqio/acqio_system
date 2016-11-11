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
      'cidade',
      'uf'
    ];

    public function fda(){
      return $this->belongsTo('App\Models\Fda','fdaid');
    }
}
