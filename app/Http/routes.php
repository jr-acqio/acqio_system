<?php

Route::get('/enviar-email','MailController@sendMail');

Route::get('/download-pdf-fda',function(){
  // Criar diretório do mês e ano referente as comissões no diretório : storage/relatorio-comissao/fdas/mês_Ano
  $directory = 'relatorio-comissao/fdas';
  $directories = Storage::directories($directory);// Obter os diretórios da pasta 'relatorio-comissao/fdas'

  $monthNum = \Carbon\Carbon::now()->format('m');
  $dateObj   = DateTime::createFromFormat('!m', $monthNum);
  $monthName = $dateObj->format('F');
  // dd($directory.'/'.$monthName.'_'.\Carbon\Carbon::now()->format('Y'));

  $folder = $directory.'/'.$monthName.'_'.\Carbon\Carbon::now()->format('Y');

  if(!in_array($directory.'/'.$monthName.'_'.\Carbon\Carbon::now()->format('Y'), $directories)){
    // Caindo aqui não existe o diretório então irei criar.
    Storage::makeDirectory($folder);
  }
  $comissoes = \App\Models\Fda::join('comissoes as c','fdas.id','=','c.fdaid')
  ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
  ->whereDate('c.data_aprovacao','<=','2016-11-30')
  ->whereDate('c.data_aprovacao','>=','2016-11-01')
  // ->where('fdas.id','=',1)
  ->groupBy('fdas.id')
  ->select('fdas.nome_razao',DB::raw('COUNT(*) as totalVendas'),
            'fdas.fdaid','fdas.id',
            DB::raw('SUM(cp.tx_instalacao) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
  ->orderBy('totalVendas','DESC')
  ->get();
  foreach ($comissoes as $key => $value) {
    $comissoes_fda = \App\Models\Fda::join('comissoes as c','c.fdaid','=','fdas.id')
    ->leftjoin('franqueados as fr','fr.id','=','c.franqueadoid')
    ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
    ->join('produtos as p','p.id','=','cp.produtoid')
    ->where('fdas.fdaid',$value->fdaid)
    ->whereDate('c.data_aprovacao','<=','2016-11-30')
    ->whereDate('c.data_aprovacao','>=','2016-11-01')
    ->select('fdas.fdaid','fdas.nome_razao','c.*','fr.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_instalacao) as totalInstalacao'),DB::raw('COUNT(*) as totalProdutos'))
    ->groupBy('c.id')
    ->orderBy('fr.franqueadoid')
    ->get();

    dispatch(
      new \App\Jobs\GeradorPdfComissoes($folder,$comissoes_fda,$value,$type = 1)
    );
  }

  // Comissões Franqueado
  $directory = 'relatorio-comissao/franqueados';
  $directories = Storage::directories($directory);// Obter os diretórios da pasta 'relatorio-comissao/fdas'

  $monthNum = \Carbon\Carbon::now()->format('m');
  $dateObj   = DateTime::createFromFormat('!m', $monthNum);
  $monthName = $dateObj->format('F');
  // dd($directory.'/'.$monthName.'_'.\Carbon\Carbon::now()->format('Y'));

  $folder = $directory.'/'.$monthName.'_'.\Carbon\Carbon::now()->format('Y');

  if(!in_array($directory.'/'.$monthName.'_'.\Carbon\Carbon::now()->format('Y'), $directories)){
    // Caindo aqui não existe o diretório então irei criar.
    Storage::makeDirectory($folder);
  }

  $comissoes_fr = \App\Models\Franqueado::join('comissoes as c','franqueados.id','=','c.franqueadoid')
  ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
  ->whereDate('c.data_aprovacao','<=','2016-11-30')
  ->whereDate('c.data_aprovacao','>=','2016-11-01')
  // ->where('franqueados.id',1)
  ->groupBy('c.franqueadoid')
  ->select('franqueados.nome_razao',DB::raw('COUNT(c.id) as totalVendas'),
            'franqueados.franqueadoid','franqueados.id',
            DB::raw('SUM(cp.tx_venda) as valor'),DB::raw('COUNT(cp.id) as totalProdutos'))
  ->orderBy('totalVendas','DESC')
  ->get();
  // dd($comissoes_fr,$folder);
  foreach ($comissoes_fr as $key => $value) {

    $comissoes = \App\Models\Franqueado::join('comissoes as c','c.franqueadoid','=','franqueados.id')
    ->join('fdas as f','f.id','=','c.fdaid')
    ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
    ->join('produtos as p','p.id','=','cp.produtoid')
    ->where('c.franqueadoid',$value->id)
    ->whereDate('c.data_aprovacao','<=','2016-11-30')
    ->whereDate('c.data_aprovacao','>=','2016-11-01')
    ->select('f.fdaid','f.nome_razao','c.*','franqueados.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_venda) as totalVenda'),DB::raw('COUNT(cp.produtoid) as totalProdutos'))
    ->groupBy('c.id')
    ->get();

    dispatch(
      new \App\Jobs\GeradorPdfComissoes($folder,$comissoes,$value,$type = 2)
    );
  }
    // dd(
    //   DB::select("
    //   SELECT fdas.fdaid, fdas.nome_razao, COUNT(*) as totalVendas, SUM(total_produtos) as totalProdutos,
    //   SUM(vttotal.valor_total) as valorTotal FROM comissoes
    //   JOIN fdas ON comissoes.fdaid = fdas.id
    //   JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_instalacao) as valor_total
    //   FROM comissoes_produto GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
    //   JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id GROUP BY fdas.id")
    // );
  return "Sucesso!";
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
    Route::resource('royalties','RoyaltiesController',['only'=>['create','store','index','destroy']]);

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
