<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Excel;
use Illuminate\Support\Facades\Input;
use App\Models\Franqueado;
use App\Models\Fda;
use File;
use DB;
use Redirect;
use Carbon\Carbon;

class FranqueadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if(isset($request->search)){
        $franqueado = $request->input('search');
        $result = Franqueado::where('documento', 'like', '%'.$franqueado.'%')
                ->orWhere(function ($query) use ($franqueado) {
                    $query->where('nome_razao','like','%'.$franqueado.'%')
                    ->orWhere('franqueadoid','like','%'.$franqueado.'%');
                    
                  })->orWhere('email','like','%'.$franqueado.'%')
                ->orderBy('nome_razao','ASC')->paginate(16);
        // dd('oi');
        return view('admin.clientes.franqueado.index')->with(['clientes'=>$result]);
      }
      return view('admin.clientes.franqueado.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clientes.franqueado.create');
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
          $updateIdFranqueado = Franqueado::where('email',$row->e_mail)->where('franqueadoid','!=',$row->franqueado)->first();
          if($updateIdFranqueado != null){
            // Se já existir o email cadastrado com o id diferente da linha atual no arquivo csv irá atualizar o franqueadoid
            $updateIdFranqueado->franqueadoid = $row->franqueado;
            $updateIdFranqueado->nome_razao = $row->nomerazao_social;
            $updateIdFranqueado->documento = $row->cpfcnpj;
            $updateIdFranqueado->cidade = $row->cidade;
            $updateIdFranqueado->uf = $row->uf;
            $updateIdFranqueado->save();
          }
          if(Franqueado::where('franqueadoid',$row->franqueado)->first() == null){
            DB::beginTransaction();
            try {
              Franqueado::create([
                'fdaid'  => \App\Models\Fda::where('fdaid',$row->fda)->first()->id,
                'franqueadoid' =>  $row->franqueado,
                'nome_razao' =>  $row->nomerazao_social,
                'documento'  =>  $row->cpfcnpj,
                'email' =>  $row->e_mail,
                'endereco'  =>  $row->endereco,
                'cep' =>  $row->cep,
                'cidade'  =>  $row->cidade,
                'uf'  =>  $row->uf
              ]);
              DB::commit();
            } catch (\Illuminate\Database\QueryException $e) {
              DB::rollBack();
              return $e->errorInfo();
            }catch(\ErrorException $e){
              DB::rollBack();
              dd($e,$row->fda);
              exit;
            }catch(ValidationException $e){
              DB::rollBack();
              return $e->errorInfo();
            }
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $franqueado = Franqueado::find($id);
      $fdas = Fda::orderBy('fdaid','ASC')->get();
      
      return view('admin.clientes.franqueado.edit')->with(['franqueado'=>$franqueado,'fdas'=>$fdas]);
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
        $franqueado = Franqueado::find($id);
        $franqueado->fdaid = $request->fdaid;
        $franqueado->franqueadoid = $request->franqueadoid;
        $franqueado->nome_razao = $request->nome_razao;
        $franqueado->documento = $request->documento;
        $franqueado->email = $request->email;
        $franqueado->cidade = $request->cidade;
        $franqueado->uf = $request->uf;
        $franqueado->save();

        return redirect('admin/franqueado/'.$id.'/edit')->with(['msg'=>'Alterações realizadas com sucesso!','class'=>'success','franqueado'=>$franqueado,'fdas'=>Fda::orderBy('fdaid','ASC')->get()]);
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

    public function getHistoricoPedidos(Request $request,$franqueadoid){
      // dd(request()->request);
      $franqueadoid = decrypt($franqueadoid);
      $franqueado = Franqueado::where('id',$franqueadoid)->first();

      $comissoes = Franqueado::join('comissoes as c','c.franqueadoid','=','franqueados.id')
      ->join('fdas as f','f.id','=','c.fdaid')
      ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
      ->join('produtos as p','p.id','=','cp.produtoid')
      ->where('c.franqueadoid',$franqueado->id)
      ->select('f.fdaid','f.nome_razao','c.*','franqueados.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_venda) as totalVenda'))
      ->groupBy('c.id')
      ->get();


      // dd($comissoes);
      return view('admin.clientes.franqueado.show-comission')
      ->with(['fr'=>$franqueado,'comissoes'=>$comissoes]);
    }
}
