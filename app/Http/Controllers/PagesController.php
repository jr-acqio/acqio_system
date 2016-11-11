<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\PagamentoCartao;
use App\Models\PagamentoBoleto;
use App\Models\Pagamento;
use App\Models\Pedidos;
use App\Models\PedidosPagamentos;
use DB;
class PagesController extends Controller
{
    public function index(){
      // dd(\Carbon\Carbon::Now()->format('Y-m-d'));
      // dd(Pedidos::whereDate('created_at','=',\Carbon\Carbon::Now()->format('Y-m-d'))->count());
      $vendasPorMes = DB::table('pagamentos_cartao')
      ->select(DB::raw('sum(valor_total) as valor'))
      ->where('status','=','Concluida')
      ->groupBy(DB::raw('MONTH(data)'))
      ->orderBy(DB::raw('MONTH(data)'), 'asc')
      ->get();

      $totalcartao = PedidosPagamentos::select(DB::raw('SUM(valor_total) as totalcartao'))
      ->join('pagamentos_cartao as pc','pc.pagamento_id','=','pedidos_pagamentos.pagamento_id')
      ->join('pedidos as p','p.id','=','pedidos_pagamentos.pedido_id')
      ->where('p.status','1')
      ->first()->totalcartao;

      $totalboleto = PedidosPagamentos::select(DB::raw('SUM(valor) as totalboleto'))
      ->join('pagamentos_boleto as pb','pb.pagamento_id','=','pedidos_pagamentos.pagamento_id')
      ->join('pedidos as p','p.id','=','pedidos_pagamentos.pedido_id')
      ->where('p.status','1')
      ->first()->totalboleto;
      // dd($totalcartao,$totalboleto);

      // dd(\Carbon\Carbon::Now()->subDays(2)->format('Y-m-d'), \Carbon\Carbon::Now()->subDay()->format('Y-m-d'));
      // dd(\Carbon\Carbon::Now()->subDay()->format('Y-m-d'), \Carbon\Carbon::Now()->format('Y-m-d'));
      $tcdAnterior = PagamentoCartao::whereBetween('data', [ \Carbon\Carbon::Now()->subDays(2)->format('Y-m-d'), \Carbon\Carbon::Now()->subDay()->format('Y-m-d') ])
      ->select(DB::raw('SUM(valor_total) as total'))
      ->first();
      $tcdAtual = PagamentoCartao::whereBetween('data', [ \Carbon\Carbon::Now()->subDay()->format('Y-m-d'), \Carbon\Carbon::Now()->format('Y-m-d') ])
      ->select(DB::raw('SUM(valor_total) as total'))
      ->first();
      if($tcdAnterior->total == null) $tcdAnterior->total = 0;
      if($tcdAtual->total == null) $tcdAtual->total = 0;

      // dd($tcdAtual->total, $tcdAnterior->total);
      if($tcdAtual->total - $tcdAnterior->total < 0 ){
        $tcd = -($tcdAtual->total / $tcdAnterior->total)*100;
      }else if($tcdAtual->total == 0 && $tcdAnterior->total == 0){
        $tcd =  0;
      }
      else{
        $tcd = ($tcdAtual->total / $tcdAnterior->total)*100;
      }
      // if($tcd == 0) $tcd = -100;
      // dd($tcd);
      return view('admin.index')->with(['vendasPorMes'=>$vendasPorMes,'totalAprovado'=>$totalcartao+$totalboleto,'tcd'=>$tcd]);
    }
    public function pageBoletoAcqio(){
      return view('site.boleto-acqio');
    }
    public function importTransacoes(){
      $transactions = PagamentoCartao::all();
      return view('admin.import-transacoes');//->with(['transactions'=>$transactions]);
    }
    public function importBoleto(){
      $boletos = PagamentoBoleto::all();
      return view('admin.import-boleto')->with(['boletos'=>$boletos]);
    }
    // public function checagemdDePagamento(){
    //
    //   return view('admin.checagem-de-pagamento');//->with([''])
    // }
}
