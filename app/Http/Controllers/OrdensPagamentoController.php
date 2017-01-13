<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\OrdemPagamento;
use Response;
use DB;

class OrdensPagamentoController extends Controller
{
    public function index()
    {
        $result = DB::select('SELECT COALESCE(fr.franqueadoid,fd.fdaid) as cliente, op.*, copTotal.totalVendas as totalVendas, COALESCE(totalRoyaltie.vRoyaltie,0) as totalRoyaltie from ordens_pagamento as op 
            LEFT JOIN fdas as fd on op.fdaid = fd.id 
            LEFT JOIN franqueados as fr on op.franqueadoid = fr.id 
            JOIN (SELECT comissoes_ordens_pagamento.idordempagamento as copid, COUNT(*) as totalVendas FROM comissoes_ordens_pagamento GROUP BY comissoes_ordens_pagamento.idordempagamento) as copTotal on copTotal.copid = op.id
            LEFT JOIN (SELECT royalties_ordem_pagamentos.idordempagamento as ropid, SUM(royalties.valor_original + royalties.cheques_devolvidos) as vRoyaltie FROM royalties_ordem_pagamentos
                JOIN royalties on royalties.id = royalties_ordem_pagamentos.idroyalties
                GROUP BY royalties_ordem_pagamentos.idordempagamento
            ) as totalRoyaltie on totalRoyaltie.ropid = op.id

        ');
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

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
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
