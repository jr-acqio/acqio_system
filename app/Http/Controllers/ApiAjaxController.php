<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\PagamentoCartao;
use App\Models\PagamentoBoleto;
use App\Models\Pagamento;
use Response;
class ApiAjaxController extends Controller
{
    public function getCheckBoleto($num){
      $existe = PagamentoBoleto::where('numero','like','%'.$num.'%')->first();
      if($existe == null || $existe->situacao == "Estornado"){
        return response()->json(0);//Codigo nao existe
      }else{
        $disponivel = PagamentoBoleto::join('pedidos_pagamentos as pp','pp.pagamento_id','=','pagamentos_boleto.pagamento_id')
        ->where('numero','like','%'.$num.'%')
        ->where('p.status','=','1')
        ->join('pedidos as p','pp.pedido_id','=','p.id')
        ->join('clientes','p.cliente_id','=','clientes.id')
        ->first();
        if($disponivel == null){
          return response()->json($existe->toJson());
        }else{
          // $cliente = Cliente_PJ::where('cliente_id',$disponivel->cliente_id)->first();
          // return response()->json($cliente->toJson());
          return response()->json($disponivel->toJson());
        }
      }
    }
    public function getCheckCodigo($codigo,$data){
      $existe = PagamentoCartao::where('codigo',$codigo)->where('data',$data)->first();
      if($existe == null){
        return response()->json(0);//Codigo nao existe
      }else{
          if($existe->status != "Concluida"){
            return response()->json($existe->toJson());
          }else{
            $disponivel = PagamentoCartao::join('pedidos_pagamentos as pp','pp.pagamento_id','=','pagamentos_cartao.pagamento_id')
            ->where('codigo',$codigo)->where('data',$data)
            ->where('p.status','=','1')
            ->join('pedidos as p','pp.pedido_id','=','p.id')
            ->join('clientes','p.cliente_id','=','clientes.id')
            ->select('clientes.*','pp.*','p.status as statuspedido','p.*','p.cliente_id','pagamentos_cartao.*')
            ->first();
            if($disponivel == null){
              return response()->json($existe->toJson());
            }else{
              // $cliente = Cliente_PJ::where('cliente_id',$disponivel->cliente_id)->first();
              // return response()->json($cliente->toJson());
              return response()->json($disponivel->toJson());
            }
          }
      }//Finish Function
    }

    public function getDetailsProduct($idproduto){
      return \App\Models\Produto::find($idproduto)->toJson();
    }
    public function postEditProduct(Request $request, $idproduto){
      $produto = \App\Models\Produto::find($idproduto);
      if($produto == null){ return response()->json(0); }

      $produto->descricao = $request->input('editDescription');
      $produto->classificacao = $request->input('editClassificacao');
      $produto->valor = $request->input('editValor');
      $produto->desconto = $request->input('editDesconto');
      $produto->tx_install = $request->input('editTxInstall');
      $produto->tx_venda = $request->input('editTxVenda');
      $produto->save();

      return response()->json($produto);
    }
}
