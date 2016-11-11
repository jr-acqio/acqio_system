<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Excel;
use Validator;
use DB;
use App\Models\Royalties;
use Redirect;
use App\Models\Franqueado;
use DateTime;

class RoyaltiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.royalties.create');
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
      $rules = [
        'arquivo' => 'required'//|mimes:application/vnd.ms-excel'
      ];
      $messages = [
        // 'arquivo.mimes' => "Insira um arquivo com formato XLS",
        'arquivo.required' => "Campo :attribute é obrigatório"
      ];
      $validator = Validator::make($request->all(),$rules,$messages);
      if($validator->fails() || $_FILES['arquivo']['type'] != "application/vnd.ms-excel"){
        return redirect::back()->witherrors($validator)->with(['msg'=>"Arquivo não permitido!",'class'=>'danger']);
      }

      Excel::load($sheet, function ($reader) use($messages) {
        $reader->each(function($row) use($messages){
          $f = Franqueado::where('franqueadoid',$row->id_franqueado)->first();
          $data = DateTime::createFromFormat('d/m/Y', $row->data_de_vencimento);

          $valor_original = str_replace(',','', $row->valor_original); //remove as virgulas
          $cheques_devolvidos = str_replace(',','', $row->cheques_devolvidos); //remove as virgulas
          if($f != null &&
            Royalties::where('data_vencimento',$data->format('Y-m-d'))->where('cliente',$row->cliente_fornecedor)
            ->where('franquia_loc',$row->franquia)->where('valor_original',$valor_original)
            ->where('cheques_devolvidos',$cheques_devolvidos)->first() == null
          ){
            $r = new Royalties;
            $r->data_vencimento = $data;
            $r->cliente = $row->cliente_fornecedor;
            $r->franquia_loc = $row->franquia;
            $r->valor_original = $valor_original;
            $r->cheques_devolvidos = $cheques_devolvidos;
            $r->franqueadoid = $f->id;
            $r->save();
          }
        });
      });
      return redirect::back()->with(['msg'=>'Dados importados com sucesso!','class'=>'success']);
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
}
