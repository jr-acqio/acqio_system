<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Transacao;
use App\Models\Cliente;
use App\Models\Fda;
use App\Models\Franqueado;
use App\Models\Produto;
use App\Models\Pedidos;
use App\Models\Pagamento;
use App\Models\Comissoes;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
class QueryController extends Controller
{

  public function getTransactions(){
    $transactions = Transacao::all();
    return response()->json($transactions);
  }

  public function getSearchVendas(Request $request){
    $cliente = $request->input('cliente');
    $produto = $request->input('produto');
    $codigo  = $request->input('codigo');
    $boleto  = $request->input('boleto');
    $status  = $request->input('status');
    $vs      = $request->input('versao_sis');
    $datainicio = implode('-', array_reverse(explode('/', $request->input('data_inicio'))));
    $datafim = implode('-', array_reverse(explode('/', $request->input('data_final'))));
    // dd($status);
    // dd($datainicio,$datafim);
    $result = Cliente::join('pedidos as p','p.cliente_id','=','clientes.id')
    ->leftjoin('pedidos_pagamentos as pp','pp.pedido_id','=','p.id')
    ->leftjoin('pagamentos_cartao as pc','pc.pagamento_id','=','pp.pagamento_id')
    ->leftjoin('pagamentos_boleto as pb','pb.pagamento_id','=','pp.pagamento_id')
    ->leftjoin('pedidos_itens as pi','pi.pedido_id','=','p.id')
    ->select('p.id as pedidoid','clientes.*','pi.produto_id as produtoid','p.status as statuspedido','p.created_at as datavenda','pc.codigo','pc.status as statuscodigo','pb.numero','pb.valor as valorboleto')
            ->when($produto,function($query) use($produto){
              return $query->where('pi.produto_id',$produto);
            })
            ->when($codigo,function($query) use($codigo){
              return $query->where('pc.codigo',$codigo);
            })
            ->when($boleto,function($query) use($boleto){
              return $query->where('pb.numero','like','%'.$boleto.'%');
            })
            ->when($status,function($query) use($status){
              if($status == "CANCELADO"){
                  $status  = 0;
              }else{
                  $status = 1;
              }
              return $query->where('p.status',$status);
            })
            ->when($vs,function($query) use($vs){
              return $query->where('p.versao_sis',$vs);
            })
            ->when($cliente, function ($query) use ($cliente){
              $query->where(function($q) use ($cliente){
                $q->orWhere('clientes.nome','like','%'.$cliente.'%');
                $q->orWhere('clientes.razao','like','%'.$cliente.'%');
              });
              return $query;
            })
            ->when($datainicio,function($query) use($datainicio,$datafim){
              if($datafim== ""){
                $datafim = \Carbon\Carbon::now()->format('Y-m-d');
              }
              return $query->whereDate('p.created_at','<=',$datafim)
              ->whereDate('p.created_at','>=',$datainicio);
            })
            ->groupBy('p.id')
            ->get();
    // dd($result);
    return view('admin.checagem.listarpedido')
    ->with(['clientes'=>Cliente::all(),'produtos'=>Produto::all(),'pedidos'=>$result,'input'=>Input::all()]);
  }
  //Pagina de Importar Transações
  public function getSearchTransacoes(Request $request){
    $codigo = $request->input('codigo');
    $status = $request->input('status');
    $datainicio = implode('-', array_reverse(explode('/', $request->input('data_inicio'))));
    $datafim = implode('-', array_reverse(explode('/', $request->input('data_final'))));
    // dd($request->all(),$datainicio,$datafim);
    $result = Pagamento::join('pagamentos_cartao as pc','pc.pagamento_id','=','pagamentos.id')
              ->when($codigo,function($query) use ($codigo){
                  return $query->where('pc.codigo',$codigo);
              })
              ->when($status,function($query) use($status){
                return $query->where('pc.status',$status);
              })
              ->when($datainicio,function($query) use($datainicio,$datafim){
                if($datafim== ""){
                  $datafim = \Carbon\Carbon::now()->format('Y-m-d');
                }
                return $query->whereDate('pc.data','<=',$datafim)
                ->whereDate('pc.data','>=',$datainicio);
              })
              ->get();
              // dd($result);
    return view('admin.import-transacoes')->with(['transactions'=>$result]);
  }

  //Pesquisar clientes
  public function getSearchClientes(Request $request){
    $cliente = $request->input('search');
    // dd($cliente);
    $result = Cliente::where('nome', 'like', '%'.$cliente.'%')
            ->orWhere(function ($query) use ($cliente) {
                $query->where('razao', 'like', '%'.$cliente.'%')
                ->orWhere(function($query) use ($cliente){
                  $query->where('cnpj','=',$cliente)
                  ->orWhere('cpf','=',$cliente);
                });
            })->orderBy('nome','ASC')->paginate(16);

    return view('admin.clientes.index')->with(['clientes'=>$result]);
  }

  //Comissões
  public function getSearchFda(Request $request){
    $fda = $request->input('search');
    // dd($cliente);
    $result = Fda::where('documento', 'like', '%'.$fda.'%')
            ->orWhere(function ($query) use ($fda) {
                $query->where('nome_razao','like','%'.$fda.'%')
                ->orWhere('fdaid','like','%'.$fda.'%');
              })
            ->orderBy('nome_razao','ASC')->paginate(16);

    return view('admin.clientes.fda.cadastrados')->with(['clientes'=>$result]);
  }
  public function getSearchFranqueado(Request $request){
    $franqueado = $request->input('search');
    // dd($cliente);
    $result = Franqueado::where('documento', 'like', '%'.$franqueado.'%')
            ->orWhere(function ($query) use ($franqueado) {
                $query->where('nome_razao','like','%'.$franqueado.'%')
                ->orWhere('franqueadoid','like','%'.$franqueado.'%');
              })
            ->orderBy('nome_razao','ASC')->paginate(16);

    return view('admin.clientes.franqueado.cadastrados')->with(['clientes'=>$result]);
  }

}
