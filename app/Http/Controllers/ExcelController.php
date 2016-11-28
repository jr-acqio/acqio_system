<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Excel;
use Illuminate\Support\Facades\Input;
use File;
use App\User;
use App\Models\Cliente;
use App\Models\Pedidos;
use App\Models\Pagamento;
use App\Models\PagamentoCartao;
use App\Models\PagamentoBoleto;
use App\Models\Comissoes;
use DB;
use Redirect;
use Carbon\Carbon;
class ExcelController extends Controller
{
    public function salvarTransacao($row,$data,$codigo,$vt,$vtl,$vf,$resumoUpload){
      DB::beginTransaction();
      try {
        $resumoUpload['novas'] += 1;
        $p = new Pagamento();
        $p->save();
        PagamentoCartao::create([
          'pagamento_id'        => $p->id,
          'data'                => $data,
          'hora'                => $row->hora,
          'codigo'              => $codigo,
          'status'              => $row->status,
          'nsu_acqio'           => $row->nsu_acqio,
          'nsu_adquirente'      => $row->nsu_adquirente,
          'tipo'                => $row->tipo,
          'bandeira'            => $row->bandeira,
          'parcelas'            => $row->parcelas,
          'tipo_parcelamento'   => $row->tipo_de_parcelamento,
          'moeda'               => $row->moeda,
          'valor_total'         => $vt,
          'valor_total_liquido' => $vtl,
          'faturamento'         => $vf,
          'origem'              => $row->origem,
          'loja'                => $row->loja,
          'documento'           => $row->documento
        ]);
        DB::commit();
      } catch (Exception $e) {
        DB::rollBack();
        $resumoUpload['novas']--;
        var_dump($e->errorInfo);
      }catch(ValidationException $e){
        DB::rollBack();
        $resumoUpload['novas']--;
        var_dump($e->errorInfo);
      }catch ( \Illuminate\Database\QueryException $e) {
        DB::rollBack();
        var_dump($e->errorInfo,'<br><p style="color:red;">Verifique se o arquivo inserido está correto.</p>');
        exit;
      }
      return $resumoUpload['novas'];
    }
    public function uploadTransacoes(){
      $sheet = Input::file('arquivo');
      $messages = array();
      $resumoUpload = array('contador'=>0,'canceladas'=>0,'concluidas'=>0,'novas'=>0);

      Excel::load($sheet, function ($reader) use (&$messages,&$resumoUpload) {
        // Loop through all rows
        $reader->each(function($row) use (&$messages,&$resumoUpload){
          $resumoUpload['contador'] += 1;
          $vt = str_replace(",",".",$row->valor_total);
          $vtl = str_replace(",",".",$row->valor_total_liquido);
          $vf = str_replace(",",".",$row->faturamento);
          $codigo = str_pad($row->codigo_de_autorizacao, 6, '0', STR_PAD_LEFT);
          $data = implode("-",array_reverse(explode("/",$row->data)));

          $transacao = PagamentoCartao::where('codigo',$codigo);//;
          // Se não existir nenhum pagamento com esse código irá salvar
          if($transacao->first() == null && $row->valor_total > 0) {
            // Caindo aqui, não existe nenhuma transação com o código testado e o valor não é negativo.
            $resumoUpload['novas'] = $this->salvarTransacao($row,$data,$codigo,$vt,$vtl,$vf,$resumoUpload);
          }else {
            if($row->valor_total < 0){
              // Caindo aqui existe uma transação negativa, ou seja, houve um estorno de transação
                $cancelar = PagamentoCartao::where('codigo',$codigo)->where('loja',$row->loja)->where('valor_total',abs($row->valor_total))->first();
                if($cancelar != null){
                  // Caindo aqui, foi encontrado a transação original no banco, na qual houve o cancelamento.
                  if($cancelar->status != "Cancelada"){
                    // Caindo aqui o status da transação ainda não tá cancelado então devemos alterar para cancelado.
                    $cancelar->update(['status'=>'Cancelada']);
                  }
                }
                $vendaCancelada = Pagamento::join('pagamentos_cartao as pc','pc.pagamento_id','=','pagamentos.id')
                ->join('pedidos_pagamentos as pp','pp.pagamento_id','=','pc.pagamento_id')
                ->join('pedidos as p','p.id','=','pp.pedido_id')
                ->join('clientes as cl','cl.id','=','p.cliente_id')
                ->where('pc.codigo',$codigo)
                ->select('p.id as idpedido','p.*','pc.codigo','pc.pagamento_id','pc.data','cl.*')
                ->first();
                if($vendaCancelada != null && $vendaCancelada->status != 0){//Se existe uma venda com o codigo da transação e se ela não ja estiver cancelada
                  if(is_null($vendaCancelada->nome)){ $nomeCliente = $vendaCancelada->razao; }else{ $nomeCliente = $vendaCancelada->nome; }
                  $arr = array('codigo'=>$codigo,'data'=>$data,'cliente'=>$nomeCliente,'pedido'=>$vendaCancelada->idpedido);
                  $messages[] = $arr;
                  Pedidos::where('id',$vendaCancelada->idpedido)->update(['status'=>0,'motivo'=>'Cancelada no ato da importação de transações','data_cancel'=>Carbon::now()]);
                }
            }
            // Se existir pagamento com esse código, verificar se as datas são iguais
            elseif($transacao->where('data',$data)->first() == null) {
              // Se caiu aqui é pq a data não é igual, então poderá salvar no banco
              $resumoUpload['novas'] = $this->salvarTransacao($row,$data,$codigo,$vt,$vtl,$vf,$resumoUpload);
            }else {
              // Se caiu aqui o código e a data são iguais então verificar o status
              if($transacao->where('data',$data)->first()->status != $row->status){
                // Caindo aqui, existe uma transação duplicada com status diferente. Então altera-se o status para o atual.
                $transacao->where('data',$data)->first()->update(['status'=>$row->status]);
              }
            }
          }
        });
      });
      // dd($resumoUpload);
      // if(isset($messages[0])){
        return redirect::back()->with(['msg'=>'Upload realizado com sucesso!','class'=>'success','transactions'=>PagamentoCartao::all(),'vendasCanceladas'=>$messages,'resumoUpload'=>$resumoUpload]);
      // }else{
      //   return redirect::back()->with(['msg'=>'Upload realizado com sucesso!','class'=>'success','transactions'=>PagamentoCartao::all(),'resumoUpload'=>$resumoUpload]);
      // }
    }

    public function uploadBoleto(){
      $sheet = Input::file('arquivo');
      $resumoUpload = array('contador'=>0,'novasBradesco'=>0,'novasBBrasil'=>0);
      Excel::load($sheet, function ($reader) use (&$resumoUpload) {
        // Loop through all rows
        $reader->each(function($row) use (&$resumoUpload) {
          $data = implode("-",array_reverse(explode("/",$row->data)));
          $numero_boleto = str_pad($row->nosso_numero, 11, '0', STR_PAD_LEFT);
          // dd($numero_boleto);
          $resumoUpload['contador'] += 1;
          if(PagamentoBoleto::where('numero','like','%'.$numero_boleto.'%')->first() == null){
            if(strlen($numero_boleto) == 11){ $resumoUpload['novasBradesco'] += 1; }
            else{ $resumoUpload['novasBBrasil'] += 1; }
            DB::beginTransaction();
            try {
              $p = new Pagamento();
              $p->save();
              PagamentoBoleto::create([
                'pagamento_id' => $p->id,
                'numero'  =>  $numero_boleto,
                'valor'   =>  $row->valor,
                'situacao'=>  $row->situacao,
                'data'    =>  $data
              ]);
              DB::commit();
            } catch (Exception $e) {
              DB::rollBack();
              $resumoUpload['novasBradesco']--;
              var_dump($e->errorInfo);
            }catch(ValidationException $e){
              DB::rollBack();
              $resumoUpload['novasBradesco']--;
              var_dump($e->errorInfo);
            }catch ( \Illuminate\Database\QueryException $e) {
              DB::rollBack();
              var_dump($e->errorInfo,'<br><p style="color:red;">Verifique se o arquivo inserido está correto.</p>');
              exit;
            }
          }
        });
      });
      return redirect::back()
      ->with(['msg'=>'Upload realizado com sucesso!','class'=>'success','boletos'=>PagamentoBoleto::all(),'resumoUpload'=>$resumoUpload]);
    }







    public function uploadMigrar(){
      $sheet = Input::file('arquivo');
      Excel::load($sheet, function ($reader) {
        // Loop through all rows
        $reader->each(function($row) {
          // dd($row);
          // $codigo = str_pad($row->codigo, 6, '0', STR_PAD_LEFT);
          // dd($row->boleto);
          $data = implode("-",array_reverse(explode("/",$row->data)));
          // dd($data);
          $tem = Pagamento::join('pagamentos_boleto as pb','pagamentos.id','=','pb.pagamento_id')
          ->join('pedidos_pagamentos as pg','pg.pagamento_id','=','pb.pagamento_id')
          ->join('pedidos as p','p.id','=','pg.pedido_id')
          ->where('numero',$row->boleto)
          ->first();
          // dd($tem);
          if($tem != null){
            // dd($tem);
            $pedido = Pedidos::find($tem->pedido_id);
            // dd($pedido);
            $pedido->created_at = $data;
            // dd($pedido);
            $pedido->save();
          }
          // dd('oi');
        });
      });
      return redirect::back()->with(['msg'=>'Sucesso','class'=>'success']);
    }
    public function uploadMigrarBoleto(){
      $sheet = Input::file('arquivo');
      Excel::load($sheet, function ($reader) {
        // Loop through all rows
        $reader->each(function($row) {

          $existe = PagamentoBoleto::where('numero',$row->boleto)->first();
          // dd($existe);
          if($existe != null){
            if(strlen($row->documento) == 14){
              $clienteexist = Cliente::where('cpf',$row->documento)->first();
              if($clienteexist == null){
                $cliente = new Cliente();
                $cliente->cpf = $row->documento;
                $cliente->nome = $row->cliente;
                $cliente->save();

                $p = new Pedidos();
                $p->cliente_id = $cliente->id;
                $p->tipovenda = "Migrado";
                $p->id_acqio = $row->acqio;
                $p->save();
                Pagamento::where('id',$existe->pagamento_id)->update(['pedido_id'=>$p->id]);
              }else{
                $p = new Pedidos();
                $p->cliente_id = $clienteexist->id;
                $p->tipovenda = "Migrado";
                $p->id_acqio = $row->acqio;
                $p->save();
                Pagamento::where('id',$existe->pagamento_id)->update(['pedido_id'=>$p->id]);
              }
            }
            elseif (strlen($row->documento) == 18) {
              $clienteexist = Cliente::where('cnpj',$row->documento)->first();
              // dd($clienteexist);
              if($clienteexist == null){
                $cliente = new Cliente();
                $cliente->cnpj = $row->documento;
                $cliente->razao = $row->cliente;
                $cliente->save();

                $p = new Pedidos();
                $p->cliente_id = $cliente->id;
                $p->tipovenda = "Migrado";
                $p->id_acqio = $row->acqio;
                $p->save();
                Pagamento::where('id',$existe->pagamento_id)->update(['pedido_id'=>$p->id]);
              }else{
                $p = new Pedidos();
                $p->cliente_id = $clienteexist->id;
                $p->tipovenda = "Migrado";
                $p->id_acqio = $row->acqio;
                $p->save();
                Pagamento::where('id',$existe->pagamento_id)->update(['pedido_id'=>$p->id]);
              }
            }
          }
          return redirect::back()->with(['msg'=>'Boletos importadas com sucesso!','class'=>'success','boletos'=>PagamentoBoleto::all()]);
        });
      });
    }

    //Importar Planilha de Comissões
    // public function importComissioes(Request $request){
    //
    // }
}
