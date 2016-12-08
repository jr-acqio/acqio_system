<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use App\Models\Franqueado;
use App\Models\Fda;
use App\Models\Comissoes;
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

      $comissoes =
      DB::select('
      SELECT fdas.id, fdas.fdaid, fdas.nome_razao, COUNT(*) as totalVendas,
SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal FROM comissoes
JOIN fdas ON comissoes.fdaid = fdas.id
JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_instalacao) as valor_total FROM comissoes_produto
GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id

WHERE date(comissoes.data_aprovacao) >= "2016-11-01" and date(comissoes.data_aprovacao) <= "2016-11-30"
GROUP BY fdas.id'
      );
      // dd($comissoes[0]);
      $count = 0;
      foreach ($comissoes as $key => $value) {
          Mail::send($template_path, ['data'=>$comissoes[$key]], function($message) use ($value){
            // Set the receiver and subject of the mail.
            $fda = Fda::where('id',$value->id)->first();
            // dd($franqueado);
            $message->to('juniorpaiva95@gmail.com');
            // $message->to($fda->email);
            // $message->cc(Fda::where('id',$franqueado->fdaid)->first()->email);
            // $message->bcc('stefano.andrei@esfera5.com.br');
            // $message->bcc('leandro.xavier@esfera5.com.br');
            // $message->bcc('juniorpaiva95@gmail.com');

            $message->subject('Relatório de Comissões - Novembro');
            // Set the sender
            // dd($message);
            $message->from('joselito.junior@esfera5.com.br','Júnior Paiva');
            $message->attach(storage_path().'/app/relatorio-comissao/fdas/December_2016/'.strtoupper($fda->fdaid).'_06-12-2016.pdf');
          });
          dd('oi');
          $count++;
      }
      return "Sucesso! Total de e-mails enviados : ".$count;
    }
    public function sendMail(){
      $template_path = 'admin.mails.emails-franqueado';

      $comissoes = DB::select('SELECT franqueados.id, franqueados.franqueadoid, franqueados.nome_razao, COUNT(*) as totalVendas,
      SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal,
      total_royalties as totalRoyalties,total_chequesdevolvidos as totalChequesDevolvidos,
      SUM(vttotal.valor_total) - total_royalties as valorFinal
      FROM comissoes
      JOIN franqueados ON comissoes.franqueadoid = franqueados.id
      JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_venda) as valor_total FROM comissoes_produto
      GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
      JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
      GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id
      JOIN (SELECT royalties.franqueadoid as rid, SUM(royalties.valor_original) as total_royalties,
      SUM(royalties.cheques_devolvidos) as total_chequesdevolvidos FROM royalties WHERE royalties.descontado != "s"
      GROUP BY rid) as rttotal ON rttotal.rid = comissoes.franqueadoid
      WHERE date(comissoes.data_aprovacao) >= "2016-11-01" and date(comissoes.data_aprovacao) <= "2016-11-30"
      GROUP BY franqueados.id');
      return view($template_path)->with(['data'=>$comissoes[0]]);
      dd($comissoes);
      $count = 0;
      foreach ($comissoes as $key => $value) {
          Mail::send($template_path, ['data'=>$comissoes[$key]], function($message) use ($value){
            // Set the receiver and subject of the mail.
            $franqueado = Franqueado::where('id',$value->id)->first();
            // dd($franqueado);
            $message->to('juniorpaiva95@gmail.com');
            // $message->to($franqueado->email);
            // $message->cc(Fda::where('id',$franqueado->fdaid)->first()->email);
            // $message->cc('financeiro1@acqiofranchising.com.br');
            // $message->cc('fda.pec1@acqiofranchising.com.br');
            // $message->bcc('stefano.andrei@esfera5.com.br');
            // $message->bcc('leandro.xavier@esfera5.com.br');
            // $message->bcc('juniorpaiva95@gmail.com');
            $message->subject('Relatório de Comissões - Novembro');
            // Set the sender
            // dd($message);
            $message->from('joselito.junior@esfera5.com.br','Júnior Paiva');
            $message->attach(storage_path().'/app/relatorio-comissao/franqueados/December_2016/'.strtoupper($franqueado->franqueadoid).'_06-12-2016.pdf');
          });
          $count++;
          dd($count);
      }
      return "Sucesso! Total de e-mails enviados : ".$count;
    }
}
