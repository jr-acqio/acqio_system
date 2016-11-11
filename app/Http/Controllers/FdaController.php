<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Excel;
use Illuminate\Support\Facades\Input;
use App\Models\Fda;
use File;
use DB;
use Redirect;
use Carbon\Carbon;

class FdaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.clientes.fda.cadastrados');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clientes.fda.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $sheet = Input::file('arquivo');
      Excel::load($sheet, function ($reader) {
        $reader->each(function($row){
          // dd($row);
          if(Fda::where('fdaid',$row->fda)->first() == null){
            Fda::create([
              'fdaid' =>  $row->fda,
              'documento'  =>  $row->cpfcnpj,
              'nome_razao' =>  $row->nomerazao_social,
              'email' =>  $row->e_mail,
              'endereco'  =>  $row->endereco,
              'cep' =>  $row->cep,
              'cidade'  =>  $row->cidade,
              'uf'  =>  $row->uf
            ]);
          }
        });
      });
      return redirect::back()->with(['msg'=>'Dados importadas com sucesso!','class'=>'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    public function getHistoricoPedidos(Request $request,$fdaid){
      // dd(request()->request);
      $fdaid = decrypt($fdaid);
      $fda = Fda::where('id',$fdaid)->first();

      $comissoes = Fda::join('comissoes as c','c.fdaid','=','fdas.id')
      ->join('franqueados as fr','fr.id','=','c.franqueadoid')
      ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
      ->join('produtos as p','p.id','=','cp.produtoid')
      ->where('c.fdaid',$fda->id)
      ->select('fdas.fdaid','fdas.nome_razao','c.*','fr.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_instalacao) as totalInstalacao'))
      ->groupBy('c.id')
      ->orderBy('fr.franqueadoid')
      ->get();

      // dd($comissoes);
      // $obj = \App\Models\Comissoes::find($comissoes[4]->comissaoid);
      // dd($obj->produtos);
      return view('admin.clientes.fda.show-comission')
      ->with(['fda'=>$fda,'comissoes'=>$comissoes]);
    }
    
}
