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
      $params = array('data_inicial' => \Carbon\Carbon::now()->subMonth()->format('Y-m-').'01',
                  'data_final' => \Carbon\Carbon::now()->subMonth()->format('Y-m-').date("t", mktime(0,0,0,\Carbon\Carbon::now()->subMonth()->format('m'),'01',\Carbon\Carbon::now()->format('Y'))));
      $month = \Carbon\Carbon::now()->subMonth()->format('m');
      // dd($month);

      //Ordens de Pagamento (franqueados) que são criadas no mÊs ATUAL referente ao mês ANTERIOR
      $comissoes = DB::select('SELECT franqueados.id, franqueados.franqueadoid, franqueados.nome_razao, COUNT(*) as totalVendas,
      SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal,
      COALESCE(total_royalties,0) as totalRoyalties,COALESCE(total_chequesdevolvidos,0) as totalChequesDevolvidos,
      SUM(vttotal.valor_total) - COALESCE(total_royalties,0) as valorFinal, op.relatorio_pdf
      FROM comissoes
      JOIN franqueados ON comissoes.franqueadoid = franqueados.id
      JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_venda) as valor_total FROM comissoes_produto
      GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
      JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
      GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id
      JOIN ordens_pagamento AS op ON op.franqueadoid = franqueados.id
       LEFT JOIN (SELECT royalties_ordem_pagamentos.idordempagamento as opid, SUM(royalties.valor_original) as total_royalties, SUM(royalties.cheques_devolvidos) as total_chequesdevolvidos FROM royalties_ordem_pagamentos JOIN royalties ON royalties.id = royalties_ordem_pagamentos.idroyalties GROUP BY opid) as rttotal ON rttotal.opid = op.id
      WHERE date(comissoes.data_aprovacao) >= "'.$params['data_inicial'].'" and date(comissoes.data_aprovacao) <= "'.$params['data_final'].'" and op.mes_ref = "'.$month.'"
      GROUP BY franqueados.id');
      // return view('admin.mails.emails-franqueado')->with(['data'=>$comissoes[18]]);
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
      SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal, op.relatorio_pdf
      FROM comissoes
      JOIN fdas ON comissoes.fdaid = fdas.id
      JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_instalacao) as valor_total FROM comissoes_produto
      GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
      JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
      GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id
      JOIN ordens_pagamento AS op ON op.fdaid = fdas.id
      WHERE date(comissoes.data_aprovacao) >= "'.$params['data_inicial'].'" and date(comissoes.data_aprovacao) <= "'.$params['data_final'].'" and op.mes_ref = "'.$month.'"
      GROUP BY fdas.id');
      
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
