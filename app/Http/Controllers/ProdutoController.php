<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Produto;

class ProdutoController extends Controller
{
  public function getIndex()
  {
      $produtos = Produto::all();
      return view('admin.produtos')->with(['produtos'=>$produtos]);
  }
  public function postCreate(Request $request){
    $produtos = Produto::all();

    $produto = new Produto();
    $produto->descricao = $request->descricao;
    if($request->classificacao == ""){
      return redirect()->back()->with(['msg'=>'Selecione uma classificação','class'=>'danger','produtos'=>$produtos]);
    }
    $produto->classificacao = $request->classificacao;
    $produto->valor = $request->valor;
    $produto->desconto = $request->desconto;
    $produto->save();
    return redirect()->back()->with(['msg'=>'Produto cadastrado com sucesso!','class'=>'success','produtos'=>$produtos]);
  }
}
