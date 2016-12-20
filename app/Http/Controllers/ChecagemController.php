<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Produto;
use App\Models\PedidosItens;
use App\Models\PedidosPagamentos;
use App\Models\Cliente;
use App\Models\Pedidos;
use App\Models\Pagamento;
use App\Models\PagamentoCartao;
use App\Models\PagamentoBoleto;
use App\Models\Fda;
use App\Models\Franqueado;
use Redirect;
use DB;
use DateTime;
use Carbon\Carbon;
class ChecagemController extends Controller
{
    // public function getMigrar(){
    //   $pagamentos = Pagamento::all();
    //   foreach ($pagamentos as $key => $value) {
    //     if($pagamentos[$key]->pedido_id != 0){
    //       PedidosPagamentos::create(['pagamento_id'=>$pagamentos[$key]->id,'pedido_id'=>$pagamentos[$key]->pedido_id]);
    //     }
    //     // if($pagamentos[$key])
    //   }
    // }
    public function getIndex(){
      $produtos = Produto::all();
      $fdas = Fda::orderBy('fdaid')->get();
      $franqueados = Franqueado::orderBy('franqueadoid')->get();
      return view('admin.checagem-de-pagamento')->with(['produtos'=>$produtos,'pedidos'=>Pedidos::all(),'fdas'=>$fdas,'franqueados'=>$franqueados]);
    }
    public function postCancelar(Request $request){
      $pedido = Pedidos::where('id',$request->pedidoid)->first();
      if($request->estorno == "sim"){
        foreach ($pedido->pagamentos as $key => $pagamento) {
          //Se for pagamento de cartão irá entrar no if
          if(Pagamento::leftjoin('pagamentos_cartao','pagamentos_cartao.pagamento_id','=','pagamentos.id')->where('pagamentos.id',$pagamento->pagamento_id)->first() != null){
            PagamentoCartao::where('pagamento_id',$pagamento->pagamento_id)->update(['status'=>'Cancelada']);
          }
          // elseif () {
          //   # code...
          // }
          
        }
      }
      // return response()->json($pedido->toSql());
      $pedido->update(['status'=>'0','motivo'=>$request->motivo,'data_cancel'=>Carbon::now()]);
      return response()->json(1);
    }
    public function getView($pedido){
      $pedido = Pedidos::where('id',$pedido)->first();
      $cliente = Cliente::join('pedidos as p','clientes.id','=','p.cliente_id')->where('p.id',$pedido->id)->first();
      // dd($cliente);
      if($pedido == null){ return "Pedido não existe"; }
      $pagamentos = DB::table('clientes as cl')->leftjoin('pedidos as p','cl.id','=','p.cliente_id')
      ->join('pedidos_pagamentos as pp','pp.pedido_id','=','p.id')
      ->join('pagamentos as pag','pag.id','=','pp.pagamento_id')
      ->leftjoin('pagamentos_cartao as pc','pp.pagamento_id','=','pc.pagamento_id')
      ->leftjoin('pagamentos_boleto as pb','pp.pagamento_id','=','pb.pagamento_id')
      ->select('pp.*','p.id as idpedido','p.status','p.observacao','cl.*',
      'pc.codigo','pc.data as cod_data','pc.status as cod_status','pc.valor_total as cod_vt','pc.hora as cod_hora','pc.origem as cod_origem', 'pb.*')
      ->where('p.id',$pedido->id)->get();

      // dd($pagamentos);

      $totalcartao = DB::table('pedidos_pagamentos as pp')
      ->leftjoin('pagamentos_cartao as pc','pc.pagamento_id','=','pp.pagamento_id')
      ->where('pedido_id',$pedido->id)->sum('valor_total');
      $totalboleto = DB::table('pedidos_pagamentos as pp')
      ->leftjoin('pagamentos_boleto as pb','pb.pagamento_id','=','pp.pagamento_id')
      ->where('pedido_id',$pedido->id)->sum('valor');
      // dd($totalcartao);
      $total = 0;
      if($totalcartao != null){
        $total = $totalcartao;
      }
      if($totalboleto != null){
        $total += $totalboleto;
      }
      // dd($pagamentos);
      return view('admin.visualizar-pedido')->with(['pedido'=>$pedido,'pagamentos'=>$pagamentos,'total'=>$total,'cliente'=>$cliente]);
    }

    public function postCreate(Request $request){
      // dd($request->all());
      if($request->t_pessoa == "PF"){
        $clientexists = Cliente::where('cpf',$request->documento)->first();
      }else{
        $clientexists = Cliente::where('cnpj',$request->documento)->first();
      }
      // dd($clientexists);
      //Cadastro do Cliente caso não exista nenhum cadastrado
      if($clientexists == null){
        $c = new Cliente();
        if($request->t_pessoa == "PF"){
          $c->cpf = $request->documento;
          $c->nome = $request->cliente;
        }else{
          $c->razao = $request->cliente;
          $c->cnpj = $request->documento;
        }
        $c->save();
      }
      //Cadastro do Pedido
      $p = new Pedidos();
      if($clientexists == null){
          $p->cliente_id = $c->id;
      }else{
        $p->cliente_id = $clientexists->id;
      }
      $p->observacao = $request->observacao;
      $p->tipovenda = $request->tipo_venda;
      $p->id_acqio = $request->id_acqio;
      $p->versao_sis = $request->versao_sis;
      $p->franqueadoid = $request->franqueado;
      $p->fdaid = $request->fda;
      $p->save();

      //Cadastrando os Itens do Pedido

      $arrayProducts = array_count_values($request->produtos);
      // dd(($arrayProducts[3]), $request->produtos);
      foreach ($arrayProducts as $key => $v) {
          $pi = new PedidosItens();
          $pi->pedido_id = $p->id;
          $pi->produto_id = $key;
          $pi->quantidade = $v;
          $pi->preco_unit = Produto::select(DB::raw('valor - desconto as valor'))->where('id','=',$key)->first()->valor;
          $pi->save();
      }
      //Registrando as formas de Pagamento
      foreach ($request->cod_auto as $key => $id) {
        if($request->cod_auto[$key] != ""){
          $date = DateTime::createFromFormat('d/m/Y', $request->date[$key]);
          $date = $date->format('Y-m-d');
          $pagamento = PagamentoCartao::where('codigo',$request->cod_auto[$key])->where('data',$date)->value('pagamento_id');
          // Pagamento::where('id',$pagamento)->update(['pedido_id'=>$p->id]);
          PedidosPagamentos::create(['pagamento_id'=>$pagamento,'pedido_id'=>$p->id]);
        }
      }
      // dd($request->all());
      foreach ($request->cod_boleto as $key => $id) {
        if($request->cod_boleto[$key] != ""){
          $pagamento = PagamentoBoleto::where('numero','like','%'.$request->cod_boleto[$key].'%')->value('pagamento_id');
          // Pagamento::where('id',$pagamento)->update(['pedido_id'=>$p->id]);
          PedidosPagamentos::create(['pagamento_id'=>$pagamento,'pedido_id'=>$p->id]);
        }
      }
      $vendas = Pedidos::all();
      return redirect::back()->with(['msg'=>'Venda registrada com Sucesso!','pedidoid'=>$p->id,'class'=>'success','pedidos'=>$vendas]);
    }

    public function getConsulta(){
      return view('admin.checagem.consulta');
    }
    public function getListarPedido(){
      return view('admin.checagem.listarpedido')->with(['produtos'=>Produto::all()]);
    }
}
