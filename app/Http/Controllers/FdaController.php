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
    public function index(Request $request)
    {
      if(isset($request->search)){
        $fda = $request->input('search');
    // dd($cliente);
        $result = Fda::where('documento', 'like', '%'.$fda.'%')
        ->orWhere(function ($query) use ($fda) {
          $query->where('nome_razao','like','%'.$fda.'%')
          ->orWhere('fdaid','like','%'.$fda.'%');
        })
        ->orWhere('email','like','%'.$fda.'%')
        ->orderBy('nome_razao','ASC')->paginate(16);
        return view('admin.clientes.fda.index')->with(['clientes'=>$result]);
      }
      return view('admin.clientes.fda.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clientes.fda.create');
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
          $updateIdFda = Fda::where('email',$row->e_mail)->where('fdaid','!=',$row->fda)->first();
          if($updateIdFda != null){
            // Se já existir o email cadastrado com o id diferente da linha atual no arquivo csv irá atualizar o franqueadoid
            $updateIdFda->fdaid = $row->fda;
            $updateIdFda->nome_razao = $row->nomerazao_social;
            $updateIdFda->documento = $row->cpfcnpj;
            $updateIdFda->cidade = $row->cidade;
            $updateIdFda->uf = $row->uf;
            $updateIdFda->save();
          }
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
      $fda = Fda::find($id);
      return view('admin.clientes.fda.edit')->with(['fda'=>$fda]);
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
      $fda = Fda::find($id);
      $fda->fdaid = $request->fdaid;
      $fda->documento = $request->documento;
      $fda->email = $request->email;
      $fda->nome_razao = $request->nome_razao;
      $fda->endereco = $request->endereco;
      $fda->cep = $request->cep;
      $fda->cidade = $request->cidade;
      $fda->uf = $request->uf;
      $fda->save();

      return redirect('admin/fda/'.$id.'/edit')->with(['msg'=>'Alterações realizadas com sucesso!','class'=>'success','fda'=>$fda]);
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
