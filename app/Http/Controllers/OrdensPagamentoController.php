<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\OrdemPagamento;
use Response;
use DB;

class OrdensPagamentoController extends Controller
{
    public function index(Request $request)
    {
        if($request->type == 'pay'){
            $result = DB::select('SELECT COALESCE(fr.franqueadoid,fd.fdaid) as cliente, op.*, copTotal.totalVendas as totalVendas, COALESCE(totalRoyaltie.vRoyaltie,0) as totalRoyaltie, concat("/admin/orders/",op.id,"/", SUBSTRING_INDEX(op.relatorio_pdf, "/", -1) ) as url from ordens_pagamento as op 
            LEFT JOIN fdas as fd on op.fdaid = fd.id 
            LEFT JOIN franqueados as fr on op.franqueadoid = fr.id 
            JOIN (SELECT comissoes_ordens_pagamento.idordempagamento as copid, COUNT(*) as totalVendas FROM comissoes_ordens_pagamento GROUP BY comissoes_ordens_pagamento.idordempagamento) as copTotal on copTotal.copid = op.id
            LEFT JOIN (SELECT royalties_ordem_pagamentos.idordempagamento as ropid, SUM(royalties.valor_original + royalties.cheques_devolvidos) as vRoyaltie FROM royalties_ordem_pagamentos
                JOIN royalties on royalties.id = royalties_ordem_pagamentos.idroyalties
                GROUP BY royalties_ordem_pagamentos.idordempagamento
            ) as totalRoyaltie on totalRoyaltie.ropid = op.id
            WHERE op.status != 0
            ');
        }else{
            $result = DB::select('SELECT COALESCE(fr.franqueadoid,fd.fdaid) as cliente, op.*, copTotal.totalVendas as totalVendas, COALESCE(totalRoyaltie.vRoyaltie,0) as totalRoyaltie, concat("/admin/orders/",op.id,"/", SUBSTRING_INDEX(op.relatorio_pdf, "/", -1) ) as url from ordens_pagamento as op 
            LEFT JOIN fdas as fd on op.fdaid = fd.id 
            LEFT JOIN franqueados as fr on op.franqueadoid = fr.id 
            JOIN (SELECT comissoes_ordens_pagamento.idordempagamento as copid, COUNT(*) as totalVendas FROM comissoes_ordens_pagamento GROUP BY comissoes_ordens_pagamento.idordempagamento) as copTotal on copTotal.copid = op.id
            LEFT JOIN (SELECT royalties_ordem_pagamentos.idordempagamento as ropid, SUM(royalties.valor_original + royalties.cheques_devolvidos) as vRoyaltie FROM royalties_ordem_pagamentos
                JOIN royalties on royalties.id = royalties_ordem_pagamentos.idroyalties
                GROUP BY royalties_ordem_pagamentos.idordempagamento
            ) as totalRoyaltie on totalRoyaltie.ropid = op.id
            WHERE op.status != 1
            ');    
        }
        
        return Response::json($result);
    }

    public function view()
    {
        return view('admin.comissoes.orders.list');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function show(OrdemPagamento $orders)
    {
        $result = DB::select('SELECT COALESCE(fr.franqueadoid,fd.fdaid) as cliente, COALESCE(fr.cidade, fd.cidade) as cidade, COALESCE(fr.endereco,fd.endereco) as endereco, COALESCE(fr.email,fd.email) as email ,op.*, copTotal.totalVendas as totalVendas, COALESCE(totalRoyaltie.vRoyaltie,0) as totalRoyaltie, concat("/admin/orders/",op.id,"/", SUBSTRING_INDEX(op.relatorio_pdf, "/", -1) ) as url, null as vendas ,null as produtos from ordens_pagamento as op 
            LEFT JOIN fdas as fd on op.fdaid = fd.id 
            LEFT JOIN franqueados as fr on op.franqueadoid = fr.id 
            JOIN (SELECT comissoes_ordens_pagamento.idordempagamento as copid, COUNT(*) as totalVendas FROM comissoes_ordens_pagamento GROUP BY comissoes_ordens_pagamento.idordempagamento) as copTotal on copTotal.copid = op.id
            LEFT JOIN (SELECT royalties_ordem_pagamentos.idordempagamento as ropid, SUM(royalties.valor_original + royalties.cheques_devolvidos) as vRoyaltie FROM royalties_ordem_pagamentos
                JOIN royalties on royalties.id = royalties_ordem_pagamentos.idroyalties
                GROUP BY royalties_ordem_pagamentos.idordempagamento
            ) as totalRoyaltie on totalRoyaltie.ropid = op.id
            WHERE op.id = '.$orders->id.'
        ');
        
        $vendas = DB::select(
            'SELECT c.*, null as produtos FROM comissoes as c
            JOIN comissoes_ordens_pagamento as cop on cop.idcomissao = c.id
            WHERE cop.idordempagamento = '.$orders->id.'
            '
        );
// dd($vendas);
        foreach ($vendas as $key => $value) {
            // dd($value);
            $produtos = DB::SELECT('SELECT p.descricao, p.tx_install, p.tx_venda from comissoes_produto as cp
                JOIN produtos as p on p.id = cp.produtoid
                WHERE cp.comissaoid = '.$value->id.'
            ');
           $value->produtos = $produtos;
        }
        
        // $obj = (object) array_merge((array) $result[0]->vendas, (array) $vendas);
        $result[0]->vendas = $vendas;
        // dd($result[0]);
        // $result[0]->vendas
        
        // return response()->json(collect($result[0]));
        return view('admin.comissoes.orders.show')->with(['order'=> collect($result[0])]);
        //return response()->json($orders);
    }

    public function update(Request $request, OrdemPagamento $orders)
    {
        if($request->params['type'] == "approvedOrder"){
            $orders->status = 1;
            $orders->save();
            return response()->json(200);
        }
        else if($request->params['type'] == 'neutralizeOrder'){
            $orders->status = 0;
            $orders->save();
            return response()->json(200);
        }
        // else{
        //     return response()->json($orders,200);
        // }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
