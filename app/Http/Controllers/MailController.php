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
    public function sendMail(){

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
      LEFT JOIN (SELECT royalties.franqueadoid as rid, SUM(royalties.valor_original) as total_royalties,
      SUM(royalties.cheques_devolvidos) as total_chequesdevolvidos FROM royalties WHERE royalties.descontado != "s"
      GROUP BY rid) as rttotal ON rttotal.rid = comissoes.franqueadoid
      WHERE date(comissoes.data_aprovacao) >= "2016-11-01" and date(comissoes.data_aprovacao) <= "2016-11-30"
      GROUP BY franqueados.id');
      // return view($template_path)->with(['data'=>$comissoes[0]]);
      // dd($comissoes[0]);
      $count = 0;
      foreach ($comissoes as $key => $value) {
        dispatch(
          new \App\Jobs\SendEmailsComissions(2,$value)
        );
        $count++;
      }


      // Emails FDA
      $comissoes =
      DB::select('SELECT fdas.id, fdas.fdaid, fdas.nome_razao, COUNT(*) as totalVendas,
      SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal FROM comissoes
      JOIN fdas ON comissoes.fdaid = fdas.id
      JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_instalacao) as valor_total FROM comissoes_produto
      GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
      JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
      GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id

      WHERE date(comissoes.data_aprovacao) >= "2016-11-01" and date(comissoes.data_aprovacao) <= "2016-11-30"
      GROUP BY fdas.id');
      // dd($comissoes[0]);
      foreach ($comissoes as $key => $value) {
          dispatch(
            new \App\Jobs\SendEmailsComissions(1,$value)
          );
          // dd('oi');
          $count++;
      }
      return redirect('/admin/dashboard')->with(['msg'=>'Estamos enviando os '.$count.' e-mails de comissão, avisaremos ao término','class'=>'info']);
    }
}
