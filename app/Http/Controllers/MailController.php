<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use App\Models\Franqueado;
use App\Models\Fda;
use Mail;

class MailController extends Controller
{
    /*
    * Email de Resumo mensal
    */
    public function sendMailToLuana(){

    }
    public function sendMailFDA(){
      $template_path = 'admin.mails.emails-fda';

      $comissoes = \App\Models\Fda::join('comissoes as c','fdas.id','=','c.fdaid')
      ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
      ->where('c.data_aprovacao','<=','2016-10-31')
      ->where('c.data_aprovacao','>=','2016-10-01')
      ->groupBy('fdas.id')
      // ->groupBy('c.id')
      ->select('fdas.nome_razao',DB::raw('COUNT(*) as totalVendas'),
                'fdas.fdaid','fdas.id',
                DB::raw('SUM(cp.tx_instalacao) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
      ->orderBy('totalVendas','DESC')
      ->get();
      // dd($comissoes);
      foreach ($comissoes as $key => $value) {
          Mail::send($template_path, ['data'=>$comissoes[$key]], function($message) use ($value){
            // Set the receiver and subject of the mail.
            $fda = Fda::where('id',$value->id)->first();
            // dd($franqueado);
            // $message->to('stefano.andrei@esfera5.com.br');
            $message->to($fda->email);
            // $message->cc(Fda::where('id',$franqueado->fdaid)->first()->email);
            $message->bcc('stefano.andrei@esfera5.com.br');

            $message->subject('Relatório de Comissões - Outubro');
            // Set the sender
            // dd($message);
            $message->from('joselito.junior@esfera5.com.br','Júnior Paiva');
            $message->attach(storage_path().'/app/relatorio-comissao/fdas/'.strtoupper($fda->fdaid).'_10-11-2016.pdf');
          });
      }
      return "Sucesso!";
    }
    public function sendMail(){

      $template_path = 'admin.mails.emails-franqueado';

      $comissoes_fr = \App\Models\Franqueado::join('comissoes as c','franqueados.id','=','c.franqueadoid')
      ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
      ->where('c.data_aprovacao','<=','2016-10-31')
      ->where('c.data_aprovacao','>=','2016-10-01')
      ->groupBy('c.franqueadoid')
      // ->groupBy('cp.produtoid')
      ->select('franqueados.nome_razao',DB::raw('COUNT(c.id) as totalVendas'),
                'franqueados.franqueadoid','franqueados.id',
                DB::raw('SUM(cp.tx_venda) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
      ->orderBy('totalVendas','DESC')
      ->get();

      // return view($template_path)->with(['data'=>$comissoes_fr[0]]);

      foreach ($comissoes_fr as $key => $value) {
          Mail::send($template_path, ['data'=>$comissoes_fr[$key]], function($message) use ($value){
            // Set the receiver and subject of the mail.
            $franqueado = Franqueado::where('id',$value->id)->first();
            // dd($franqueado);
            // $message->to('stefano.andrei@esfera5.com.br');
            $message->to($franqueado->email);
            // $message->cc(Fda::where('id',$franqueado->fdaid)->first()->email);
            $message->bcc('stefano.andrei@esfera5.com.br');

            $message->subject('Relatório de Comissões - Outubro');
            // Set the sender
            // dd($message);
            $message->from('joselito.junior@esfera5.com.br','Júnior Paiva');
            $message->attach(storage_path().'/app/relatorio-comissao/franqueados/'.strtoupper($franqueado->franqueadoid).'_10-11-2016.pdf');
          });
      }
    }
}
