<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Excel;
use Illuminate\Support\Facades\Input;
use App\Models\Franqueado;
use App\Models\Fda;
use App\Models\Comissoes;
use App\Models\ComissaoProduto;
use App\Models\Produto;
use File;
use DB;
use Redirect;
use Carbon\Carbon;
use DateTime;
use Validator;
use Mail;
use Storage;

class ComissoesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fdas = Fda::orderBy('fdaid')->get();
        $franqueados = Franqueado::orderBy('franqueadoid')->get();
        return view('admin.comissoes.consulta-comissoes')->with(['fdas'=>$fdas,'franqueados'=>$franqueados]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.comissoes.create');
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
      // dd('oi');
      $arrayMessages = array();
      // dd($messages);
      $counter = 0;
      Excel::load($sheet, function ($reader) use(&$arrayMessages,&$counter) {

        $reader->each(function($row) use(&$arrayMessages,&$counter){
          $counter++;
          $frag_data_aprovacao = explode(" ",$row->datahora_de_finalizacao);
          $data_aprovacao = $frag_data_aprovacao[0];//." ".$frag_data_aprovacao[2].":00";

          $data = DateTime::createFromFormat('d/m/Y', $row->data_de_cadastro);

          // Dividindo os dispositivos em array
          $dispositivos = array_map('trim', explode(',', $row->modelo_pos));
          /*
          Varrendo o array de dispositivos para verificar se há dispositivos ou não, ou se existem dispositivos
          na planilha que não há tags nos produtos do banco de dados.
          */
          // Verificando no if abaixo se a linha corrente já foi inserida no banco
          // dd($row);
          if(Fda::where('fdaid',$row->fda)->first() == null){
            $arrayMessages[] = array(
              'linha'=>$counter+1,
              'motivo'=>"Fda não encontrado no sistema",
            );
          }
          if(Franqueado::where('franqueadoid',$row->franqueado)->first() == null && $row->franqueado != ""){
            $arrayMessages[] = array(
              'linha'=>$counter+1,
              'motivo'=>"Franqueado não encontrado no sistema",
            );
          }
          if($dispositivos[0] == '-'){
            $arrayMessages[] = array(
              'linha'=>$counter+1,
              'motivo'=>"Produto não existe",
            );
          }
          $dataehora = array_map('trim', explode('-', $row->datahora_de_finalizacao));
          // dd($dataehora,$counter,count($dataehora));
          if(count($dataehora) == 1){
            $d = $dataehora[0].' '.'00:00:00';
          }elseif(count($dataehora) == 2){
            $d = $dataehora[0].' '.$dataehora[1].':00';
          }else{
            $d = $dataehora[0].' '.$dataehora[1].':00';
          }
          $data_aprov = Carbon::create($d[6].$d[7].$d[8].$d[9], $d[3].$d[4], $d[0].$d[1], $d[11].$d[12], $d[14].$d[15], $d[17].$d[18], 'America/Fortaleza');
          // dd($data_aprov,$data_aprov->format('Y-m-d H:i:s'),$counter);
          // dd($data_aprov,Comissoes::where('data_aprovacao',$data_aprov->format('Y-m-d H:i:s'))->first());
          if(
          Comissoes::join('fdas as fd','fd.id','=','comissoes.fdaid')
          ->leftjoin('franqueados as fr','fr.id','=','comissoes.franqueadoid')
          ->where('data_cadastro',$data->format('Y-m-d'))
          ->where('nome_cliente',$row->nomerazao_social)
          ->where('fd.fdaid',$row->fda)
          ->where('data_aprovacao',$data_aprov->format('Y-m-d H:i:s'))
          ->orWhere(function($query) use ($row,$data){
            $query->whereNull('fr.franqueadoid');
            $query->where('nome_cliente',$row->nomerazao_social);
            $query->where('data_cadastro',$data->format('Y-m-d'));
            $query->where('fd.fdaid',$row->fda);
          })
          ->first() == null
          ){
            if($row->franqueado == "" && Fda::where('fdaid',$row->fda)->first() != null){
              $arrayMessages[] = array(
                'linha'=>$counter+1,
                'motivo'=>"Coluna Franqueado em branco, será considerado Franquia Piloto",
              );
            }
            if(Fda::where('fdaid',$row->fda)->first() != null
            && count($dispositivos) > 0 && $dispositivos[0] != '-'
            ){
              $c = new Comissoes();
              $c->data_cadastro = $data;
              $c->data_aprovacao = $data_aprov;
              $c->nome_cliente = $row->nomerazao_social;
              $c->cidade = $row->cidade;
              $c->uf = $row->estado;
              $c->fdaid = Fda::where('fdaid',$row->fda)->first()->id;
              if(Franqueado::where('franqueadoid',$row->franqueado)->first() != null){
                $c->franqueadoid = Franqueado::where('franqueadoid',$row->franqueado)->first()->id;
              }
              $c->serial = $row->serial;
              $c->nf = $row->numero_nfe;
              $c->save();

              // $dispositivos = array_map('trim', explode(',', $row->modelo_pos));
              foreach ($dispositivos as $k => $v) {
                $cp = new ComissaoProduto();
                $cp->comissaoid = $c->id;
                $cp->produtoid = Produto::where('tags','like','%'.$v.'%')->first()->id;
                $cp->tx_instalacao = Produto::where('tags','like','%'.$v.'%')->first()->tx_install;
                $cp->tx_venda = Produto::where('tags','like','%'.$v.'%')->first()->tx_venda;
                $cp->save();
              }
            }
          }
        });
      });
      return redirect::back()->with(['msg'=>'Dados importados com sucesso!','class'=>'success','erros'=>$arrayMessages]);
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
    public function filtrarComissao(Request $request){
      $periodo =  explode(" ",$request->daterange);
      $inicio = implode("-",array_reverse(explode("/",$periodo[0])));
      $final = implode("-",array_reverse(explode("/",$periodo[2])));
      $rules = [
        'tipo' => 'required',
        'cliente' => 'required',
        'daterange' => 'required'
      ];
      $messages = [
        'tipo.required' => 'Campo "TIPO CLIENTE" obrigatório',
        'cliente.required' => 'Campo "CLIENTE" obrigatório',
        'daterange.required' => 'Campo "PERIODO" obrigatório'
      ];
      $validator = Validator::make($request->all(),$rules,$messages);
      if($validator->fails()){
        return redirect::back()->withErrors($validator);
      }else{
        if($request->tipo == 'fda'){
          $fda = Fda::where('fdaid',$request->cliente)->first();
          // dd($final,$inicio);
          $comissoes = Fda::join('comissoes as c','c.fdaid','=','fdas.id')
          ->leftjoin('franqueados as fr','fr.id','=','c.franqueadoid')
          ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
          ->join('produtos as p','p.id','=','cp.produtoid')
          ->where('fdas.fdaid',$request->cliente)
          ->whereDate('c.data_aprovacao','<=',$final)
          ->whereDate('c.data_aprovacao','>=',$inicio)
          ->select('fdas.fdaid','fdas.nome_razao','c.*','fr.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_instalacao) as totalInstalacao'),DB::raw('COUNT(*) as totalProdutos'))
          ->groupBy('c.id')
          // ->groupBy('cp.produtoid')
          ->orderBy('fr.franqueadoid')
          ->get();
          // dd($comissoes);
          return view('admin.comissoes.consulta-comissoes')
          ->with([
            'fda'=>$fda,
            'comissoes'=>$comissoes,
            'fdas'=>Fda::orderBy('fdaid')->get(),
            'franqueados'=>Franqueado::orderBy('franqueadoid')->get(),
            'inputs'=> Input::all()
          ]);
        }else{
          $franqueado = Franqueado::where('franqueadoid',$request->cliente)->first();
          $comissoes = Franqueado::join('comissoes as c','c.franqueadoid','=','franqueados.id')
          ->join('fdas as f','f.id','=','c.fdaid')
          ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
          ->join('produtos as p','p.id','=','cp.produtoid')
          ->where('c.franqueadoid',$franqueado->id)
          ->whereDate('c.data_aprovacao','<=',$final)
          ->whereDate('c.data_aprovacao','>=',$inicio)
          ->select('f.fdaid','f.nome_razao','c.*','franqueados.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_venda) as totalVenda'),DB::raw('COUNT(cp.produtoid) as totalProdutos'))
          ->groupBy('c.id')
          // ->groupBy('cp.produtoid')
          ->get();
          // dd($comissoes);
          return view('admin.comissoes.consulta-comissoes')
          ->with([
            'fr'=>$franqueado,
            'comissoes'=>$comissoes,
            'fdas'=>Fda::orderBy('fdaid')->get(),
            'franqueados'=>Franqueado::orderBy('franqueadoid')->get(),
            'inputs'=> Input::all()
          ]);
        }
        // dd($comissoes);
      }
    }

    public function viewListarTodasComissoes(){
      $fdas = Fda::orderBy('fdaid')->get();
      $franqueados = Franqueado::orderBy('franqueadoid')->get();
      return view('admin.comissoes.list')->with(['fdas'=>$fdas,'franqueados'=>$franqueados]);
    }
    public function listarTodasComissoes(Request $request){
      $periodo =  explode(" ",$request->daterange);
      $inicio = implode("-",array_reverse(explode("/",$periodo[0])));
      $final = implode("-",array_reverse(explode("/",$periodo[2])));
      $emails = array('to'=>$request->to,'cc'=>array());
      if(isset($request->copias)){
        foreach ($request->copias as $key => $value) {
          $emails['cc'][] = $value;
        }
      }
      // dd($emails);
      $comissoes = Fda::join('comissoes as c','fdas.id','=','c.fdaid')
      ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
      ->whereDate('c.data_aprovacao','<=',$final)
      ->whereDate('c.data_aprovacao','>=',$inicio)
      ->groupBy('fdas.id')
      // ->groupBy('c.id')
      ->select('fdas.nome_razao',DB::raw('COUNT(*) as totalVendas'),
                'fdas.fdaid','fdas.id',
                DB::raw('SUM(cp.tx_instalacao) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
      ->orderBy('totalVendas','DESC')
      ->get();
      // $comissoes_fda = DB::select("SELECT fdas.fdaid, fdas.nome_razao, COUNT(*) as totalVendas, SUM(total_produtos) as totalProdutos,
      // SUM(vttotal.valor_total) as valorTotal FROM comissoes
      // JOIN fdas ON comissoes.fdaid = fdas.id
      // JOIN (SELECT comissoes_produto.comissaoid as vvid,
      // SUM(comissoes_produto.tx_instalacao) as valor_total FROM comissoes_produto GROUP BY vvid)
      // as vttotal ON vttotal.vvid = comissoes.id
      // JOIN (SELECT comissoes_produto.comissaoid as vid,
      // COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto GROUP BY vid )
      // as pttotal ON pttotal.vid = comissoes.id GROUP BY fdas.id");

      $comissoes_fr = Franqueado::join('comissoes as c','franqueados.id','=','c.franqueadoid')
      ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
      ->whereDate('c.data_aprovacao','<=',$final)
      ->whereDate('c.data_aprovacao','>=',$inicio)
      ->groupBy('c.franqueadoid')
      // ->groupBy('cp.produtoid')
      ->select('franqueados.nome_razao',DB::raw('COUNT(c.id) as totalVendas'),
                'franqueados.franqueadoid','franqueados.id',
                DB::raw('SUM(cp.tx_venda) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
      ->orderBy('totalVendas','DESC')
      ->get();


      // Mail to Luana
      if(isset($request->mail)){
        $data = array('c_fda'=>$comissoes,'c_fr'=>$comissoes_fr,'datafinal'=>$final,'datainicial'=>$inicio,'data_message'=>$request->mensagem);
        // Path or name to the blade template to be rendered
        // dd($data);
        $template_path = 'admin.mails.relatorio-mensal';
        Mail::send($template_path, compact('data'), function($message) use ($data,$periodo,$emails){
          // Set the receiver and subject of the mail.
          $message->to($emails['to'], '');
          foreach ($emails['cc'] as $key => $value) {
            $message->cc($value);
          }
          $message->subject('Relatório de Comissões - '.$periodo[0].' à '.$periodo[2]);
          // Set the sender
          // dd($message);
          $message->from('joselito.junior@esfera5.com.br','Joselito Júnior');
          // $message->attach(public_path().'/uploads/correcao_comissoes_outubro.xls');
        });
        return view('admin.comissoes.list')
        ->with([
          'msg'=>'Email enviado com sucesso!',
          'class'=>'success',
          'comissoes'=>$comissoes,
          'comissoes_fr'=>$comissoes_fr,
          'fdas'=>Fda::orderBy('fdaid')->get(),
          'franqueados'=>Franqueado::orderBy('franqueadoid')->get(),
          'inputs'=> Input::all(),
          'datafinal'=>$final,
          'datainicial'=>$inicio
        ]);
      }

      return view('admin.comissoes.list')
      ->with([
        'comissoes'=>$comissoes,
        'comissoes_fr'=>$comissoes_fr,
        'fdas'=>Fda::orderBy('fdaid')->get(),
        'franqueados'=>Franqueado::orderBy('franqueadoid')->get(),
        'inputs'=> Input::all(),
        'datafinal'=>$final,
        'datainicial'=>$inicio
      ]);
    }

    public function downloadExcel(){
      Excel::create('Filename', function($excel) {
          $excel->sheet('Sheetname', function($sheet) {

          });
      })->export('xls');
    }



    // Gerar os PDFS
    public function gerarPdfs(){
      
    }
}
