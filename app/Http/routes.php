<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/enviar-email','MailController@sendMail');

Route::get('/download-pdf-fda',function(){
  $directory = 'relatorio-comissao/fdas';
  $comissoes = \App\Models\Fda::join('comissoes as c','fdas.id','=','c.fdaid')
  ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
  ->whereDate('c.data_aprovacao','<=','2016-10-31')
  ->whereDate('c.data_aprovacao','>=','2016-10-01')
  ->where('fdas.id','=',6)
  ->groupBy('fdas.id')
  // ->groupBy('c.id')
  ->select('fdas.nome_razao',DB::raw('COUNT(*) as totalVendas'),
            'fdas.fdaid','fdas.id',
            DB::raw('SUM(cp.tx_instalacao) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
  ->orderBy('totalVendas','DESC')
  ->get();
  // dd($comissoes);
  foreach ($comissoes as $key => $value) {
    $directories = Storage::directories($directory);
    // dd($value);
    // if(in_array($directory.$value->fdaid, $directories)){
    //   // dd('sim');
    // }else{
    //   Storage::makeDirectory($directory.'/'.$value->franqueadoid);
    // }

    $comissoes_fda = \App\Models\Fda::join('comissoes as c','c.fdaid','=','fdas.id')
    ->leftjoin('franqueados as fr','fr.id','=','c.franqueadoid')
    ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
    ->join('produtos as p','p.id','=','cp.produtoid')
    ->where('fdas.fdaid',$value->fdaid)
    ->whereDate('c.data_aprovacao','<=','2016-10-31')
    ->whereDate('c.data_aprovacao','>=','2016-10-01')
    ->select('fdas.fdaid','fdas.nome_razao','c.*','fr.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_instalacao) as totalInstalacao'),DB::raw('COUNT(*) as totalProdutos'))
    ->groupBy('c.id')
    ->orderBy('fr.franqueadoid')
    ->get();
    // dd($comissoes_fda);
    $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fda'=>$comissoes_fda,'fda'=>\App\Models\Fda::where('fdaid',$value->fdaid)->first()]);
    // return $pdf->stream();
    $pdf->save(storage_path().'/app/relatorio-comissao/fdas'.strtoupper($value->fda).'/'.strtoupper($value->fdaid).'_'.\Carbon\Carbon::now()->format('d-m-Y').'.pdf');
  }
  return "Sucesso!";
});
Route::get('/download-pdf',function(){
  // $files = Storage::files('relatorio-comissao/fdas');
  $directory = 'relatorio-comissao/franqueados';

  $comissoes_fr = \App\Models\Franqueado::join('comissoes as c','franqueados.id','=','c.franqueadoid')
  ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
  ->whereDate('c.data_aprovacao','<=','2016-10-31')
  ->whereDate('c.data_aprovacao','>=','2016-10-01')
  ->where('franqueados.id',86)
  ->groupBy('c.franqueadoid')
  // ->groupBy('cp.produtoid')
  ->select('franqueados.nome_razao',DB::raw('COUNT(c.id) as totalVendas'),
            'franqueados.franqueadoid','franqueados.id',
            DB::raw('SUM(cp.tx_venda) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
  ->orderBy('totalVendas','DESC')
  ->get();
  // dd($comissoes_fr);
  foreach ($comissoes_fr as $key => $value) {
    $directories = Storage::directories($directory);
    // dd($value);
    // if(in_array($directory.$value->fdaid, $directories)){
    //   // dd('sim');
    // }else{
    //   Storage::makeDirectory($directory.'/'.$value->franqueadoid);
    // }

    $comissoes = \App\Models\Franqueado::join('comissoes as c','c.franqueadoid','=','franqueados.id')
    ->join('fdas as f','f.id','=','c.fdaid')
    ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
    ->join('produtos as p','p.id','=','cp.produtoid')
    ->where('c.franqueadoid',$value->id)
    ->whereDate('c.data_aprovacao','<=','2016-10-31')
    ->whereDate('c.data_aprovacao','>=','2016-10-01')
    ->select('f.fdaid','f.nome_razao','c.*','franqueados.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_venda) as totalVenda'),DB::raw('COUNT(cp.produtoid) as totalProdutos'))
    ->groupBy('c.id')
    ->get();
    // dd($comissoes);
    // $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fda'=>$comissoes_fda,'fda'=>$value->fdaid]);
    $pdf = PDF::loadView('admin.comissoes.pdf-comissao',['comissoes_fr'=>$comissoes,'franqueado'=>\App\Models\Franqueado::where('franqueadoid',$value->franqueadoid)->first()]);
    // return $pdf->stream();

    $pdf->save(storage_path().'/app/relatorio-comissao/franqueados'.strtoupper($value->franqueado).'/'.strtoupper($value->franqueadoid).'_'.\Carbon\Carbon::now()->format('d-m-Y').'.pdf');
    // dd(storage_path());
    // $pdf->download($value->fdaid.'.pdf');
    // $pdf->download($value->fdaid.'/relatorio-comissao_01/10/2016_a_31/10/2016.pdf');
  }
  return 'Sucesso';
});
//ajax
Route::get('/login','AuthController@index');
Route::group(['prefix'=> 'api'],function(){
  Route::get('ajax/codigo/{codigo}/data/{data}','ApiAjaxController@getCheckCodigo');
  Route::get('ajax/boleto/{num}','ApiAjaxController@getCheckBoleto');

  // Route for Franqueados relationship with Fda
  Route::get('/ajax/fda/{fda}','ApiAjaxController@getFranqueados');
  //Route Edit Products
  Route::get('ajax/produto-details/{idproduto}','ApiAjaxController@getDetailsProduct');
  Route::post('ajax/editProduct/{idproduto}','ApiAjaxController@postEditProduct');
});
// Route::post("/migrar","ExcelController@uploadMigrar");
// Route::post("/migrarBoleto","ExcelController@uploadMigrarBoleto");
//Boletos
Route::get('/boleto-acqio','PagesController@pageBoletoAcqio');

Route::get('/','AuthController@index');
Route::post('/logar','AuthController@logar');
Route::get('/criar-conta','AuthController@getCriarConta');
Route::post('/criar-conta','AuthController@criarConta');
Route::get('/logout','AuthController@logout');
Route::group(['middleware' => 'auth', 'prefix' => 'admin'],function(){
  Route::get('dashboard','PagesController@index');
  Route::controller('produtos','ProdutoController',
    array(
        'postCreate'     => 'produtos.create',
    ));
  Route::controller('search','QueryController',
    array(
        'getSearchVendas'       => 'search.vendas',
        'getSearchTransacoes' => 'search.transacoes',
        'getSearchClientes'    => 'search.cliente',
        'getFilterComissions' =>  'filter.comissions',
        'getSearchFda'=>  'search.fda',
        'getSearchFranqueado'=>  'search.franqueado',
    ));
    // Route::controller('comissoes','ComissoesController',
    //   array(
    //       'getComissoes'    =>  'comissoes.comissoes'
    // ));
    Route::controller('clientes','ClienteController',
      array(
          'getClientes'    =>  'clientes.clientes',
          'getHistoricoPedidos' =>  'clientes.historico-pedidos'


    ));
    Route::resource('fda','FdaController');
    Route::get('fda/historico-pedidos/{id}','FdaController@getHistoricoPedidos');
    // Route::get('fda/historico-pedidos/{id}/print','FdaController@getHistoricoPedidosPrint');
    Route::resource('franqueado','FranqueadoController');
    Route::get('franqueado/historico-pedidos/{id}','FranqueadoController@getHistoricoPedidos');



    Route::get('comissoes/filtrar','ComissoesController@filtrarComissao');
    Route::get('comissoes/list','ComissoesController@viewListarTodasComissoes');
    Route::get('comissoes/list/all','ComissoesController@listarTodasComissoes');
    Route::get('comissoes/list/download','ComissoesController@downloadExcel');
    // Route::get('comissoes/list/email')
    Route::resource('comissoes','ComissoesController',['except'=>['update','edit','show']]);
    Route::resource('royalties','RoyaltiesController',['only'=>['create','store']]);

  // Obter Transações
  Route::get('/getTransactions',['as' => 'get-transactions', 'uses' => 'QueryController@getTransactions']);
  //Importações
  Route::group(['prefix'=> 'imports'], function () {
    Route::get('import-transacoes', ['as' => 'imports/import-transacoes', 'uses' => 'PagesController@importTransacoes']);
    Route::post('upload-transacoes', ['as' => 'imports/upload-transacoes', 'uses' => 'ExcelController@uploadTransacoes']);
    //Boletos
    Route::get('import-boleto', ['as' => 'imports/import-boleto', 'uses' => 'PagesController@importBoleto']);
    Route::post('upload-boleto', ['as' => 'imports/upload-boleto', 'uses' => 'ExcelController@uploadBoleto']);
    //Comissoes
    Route::post('upload-comissoes', ['as' => 'imports/upload-comissoes', 'uses' => 'ExcelController@importComissioes']);

  });
  Route::controller('checagem','ChecagemController',
  array(
      'postCreate'        => 'checagem.create',
      'getView'           => 'chegagem.view',
      'postCancelar'       => 'checagem.cancelar',
      'getConsultaPedido' => 'checagem.consulta',
      'getListarPedido'   => 'checagem.listar',
  ));

  // Rotas para Mail

});
