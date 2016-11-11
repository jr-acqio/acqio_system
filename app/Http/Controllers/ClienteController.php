<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Hash;
use DB;

class ClienteController extends Controller
{
  public function getClientes(){
    return view('admin.clientes.index');
  }

  public function getHistoricoPedidos(Request $request){
    // echo $request->input('cliente-id');
    $clienteid = decrypt($request->input('cliente-id'));
    $cliente = \App\Models\Cliente::find($clienteid);

    $result = \App\Models\Cliente::join('pedidos as p','p.cliente_id','=','clientes.id')
    ->leftjoin('pedidos_itens as pi','pi.pedido_id','=','p.id')
    ->where('p.cliente_id',$clienteid)
    ->select('p.*','pi.quantidade',DB::raw('SUM(pi.preco_unit*pi.quantidade) as valortotal'))
    ->groupBy('p.id')
    ->get();
    // dd($result);
    return view('admin.clientes.historico-pedidos')->with(['history'=>$result,'cliente'=>$cliente]);
  }
}
