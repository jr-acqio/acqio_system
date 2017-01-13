<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\OrdemPagamento;
use Response;

class OrdensPagamentoController extends Controller
{
    public function index()
    {
        // $orders_fda = OrdemPagamento::whereNotNull('fdaid')->get();
        // $orders_franqueado = OrdemPagamento::whereNotNull('franqueadoid')->get();
        // return view('admin.comissoes.orders.list')->with(['orders_fda'=>$orders_fda,'orders_franqueado'=>$orders_franqueado]);
        return Response::json(OrdemPagamento::all());
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
