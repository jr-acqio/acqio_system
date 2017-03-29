<?php
use Vinkla\Pusher\Facades\Pusher;
Route::get('/enviar-email','MailController@sendMail');

Route::get('/teste',function(){
  $directory = 'relatorio-comissao/fdas';
  $directories = Storage::directories($directory);// Obter os diretórios da pasta 'relatorio-comissao/fdas'

  $params = array('data_inicial' => \Carbon\Carbon::now()->subMonth()->format('Y-m-').'01',
                  'data_final' => \Carbon\Carbon::now()->subMonth()->format('Y-m-').date("t", mktime(0,0,0,\Carbon\Carbon::now()->subMonth()->format('m'),'01',\Carbon\Carbon::now()->format('Y'))));


  if(\Carbon\Carbon::now()->subMonth()->format('m') == 12){
    $folder_year = $directory.'/'.\Carbon\Carbon::now()->subYear()->format('Y');
  }else{
    $folder_year = $directory.'/'.\Carbon\Carbon::now()->format('Y');
  }
  $folder_month = $folder_year.'/'.mes_extenso(\Carbon\Carbon::now()->subMonth()->format('m'));


  if(!in_array($folder_year,$directories)){
    // Caindo aqui não existe o diretório do ano, ou seja, a pasta referente ao ano.
    Storage::makeDirectory($folder_year);
  }
  //Verificar agora se existe a pasta referente ao mês do ano corrente.
  if(!in_array($folder_month,Storage::directories($folder_year))){
    Storage::makeDirectory($folder_month);
  }
  $comissoes = DB::select('SELECT fdas.id, fdas.fdaid, fdas.nome_razao, COUNT(*) as totalVendas,
  SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal FROM comissoes
  JOIN fdas ON comissoes.fdaid = fdas.id
  JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_instalacao) as valor_total FROM comissoes_produto
  GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
  JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
  GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id
  WHERE date(comissoes.data_aprovacao) >= "'.$params['data_inicial'].'" and date(comissoes.data_aprovacao) <= "'.$params['data_final'].'"
  GROUP BY fdas.id'
  );
  foreach ($comissoes as $key => $value) {
    $comissoes_fda = \App\Models\Fda::join('comissoes as c','c.fdaid','=','fdas.id')
    ->leftjoin('franqueados as fr','fr.id','=','c.franqueadoid')
    ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
    ->join('produtos as p','p.id','=','cp.produtoid')
    ->where('fdas.fdaid',$value->fdaid)
    ->whereDate('c.data_aprovacao','<=',$params['data_final'])
    ->whereDate('c.data_aprovacao','>=',$params['data_inicial'])
    ->select('fdas.fdaid','fdas.nome_razao','c.*','fr.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_instalacao) as totalInstalacao'),DB::raw('COUNT(*) as totalProdutos'))
    ->groupBy('c.id')
    ->orderBy('fr.franqueadoid')
    ->get();

    dispatch(
      new \App\Jobs\GeradorPdfComissoes($folder_month,$params,$comissoes_fda,$value,$type = 1)
    );

  }
  // Comissões Franqueado
  $directory = 'relatorio-comissao/franqueados';
  $directories = Storage::directories($directory);// Obter os diretórios da pasta 'relatorio-comissao/fdas'
  if(\Carbon\Carbon::now()->subMonth()->format('m') == 12){
    $folder_year = $directory.'/'.\Carbon\Carbon::now()->subYear()->format('Y');
  }else{
    $folder_year = $directory.'/'.\Carbon\Carbon::now()->format('Y');
  }
  $folder_month = $folder_year.'/'.mes_extenso(\Carbon\Carbon::now()->subMonth()->format('m'));

  if(!in_array($folder_year,$directories)){
    // Caindo aqui não existe o diretório do ano, ou seja, a pasta referente ao ano.
    Storage::makeDirectory($folder_year);
  }
  //Verificar agora se existe a pasta referente ao mês do ano corrente.
  if(!in_array($folder_month,Storage::directories($folder_year))){
    Storage::makeDirectory($folder_month);
  }

  $comissoes_fr = DB::select('SELECT franqueados.id, franqueados.franqueadoid, franqueados.nome_razao, COUNT(*) as totalVendas,
  SUM(total_produtos) as totalProdutos, SUM(vttotal.valor_total) as valorTotal,
  COALESCE(total_royalties,0) as totalRoyalties,COALESCE(total_chequesdevolvidos,0) as totalChequesDevolvidos,
  SUM(vttotal.valor_total) - COALESCE(total_royalties,0) - COALESCE(total_chequesdevolvidos,0) as valorFinal
  FROM comissoes
  JOIN franqueados ON comissoes.franqueadoid = franqueados.id
  JOIN (SELECT comissoes_produto.comissaoid as vvid, SUM(comissoes_produto.tx_venda) as valor_total FROM comissoes_produto
  GROUP BY vvid) as vttotal ON vttotal.vvid = comissoes.id
  JOIN (SELECT comissoes_produto.comissaoid as vid, COUNT(comissoes_produto.produtoid) as total_produtos FROM comissoes_produto
  GROUP BY vid ) as pttotal ON pttotal.vid = comissoes.id
  LEFT JOIN (SELECT royalties.franqueadoid as rid, SUM(royalties.valor_original) as total_royalties,
  SUM(royalties.cheques_devolvidos) as total_chequesdevolvidos FROM royalties WHERE royalties.descontado != "s"
  GROUP BY rid) as rttotal ON rttotal.rid = comissoes.franqueadoid
  WHERE date(comissoes.data_aprovacao) >= "'.$params['data_inicial'].'" and date(comissoes.data_aprovacao) <= "'.$params['data_final'].'"
  GROUP BY franqueados.id');
  // dd($comissoes_fr);
  foreach ($comissoes_fr as $key => $value) {
    $comissoes = \App\Models\Franqueado::join('comissoes as c','c.franqueadoid','=','franqueados.id')
    ->join('fdas as f','f.id','=','c.fdaid')
    ->join('comissoes_produto as cp','cp.comissaoid','=','c.id')
    ->join('produtos as p','p.id','=','cp.produtoid')
    ->where('c.franqueadoid',$value->id)
    ->whereDate('c.data_aprovacao','<=',$params['data_final'])
    ->whereDate('c.data_aprovacao','>=',$params['data_inicial'])
    ->select('f.fdaid','f.nome_razao','c.*','franqueados.*','cp.*','p.descricao',DB::raw('SUM(cp.tx_venda) as totalVenda'),DB::raw('COUNT(cp.produtoid) as totalProdutos'))
    ->groupBy('c.id')
    ->get();
    // $f = \App\Models\Franqueado::where('id',$value->id)->first();
    // dd($f->hasRoyalties()->sum('valor_original','cheques_devolvidos'));
    dispatch(
      new \App\Jobs\GeradorPdfComissoes($folder_month,$params,$comissoes,$value,$type = 2)
    );
  }
  // Pusher::trigger('my-channel', 'generate_pdfs',array('message' => 'Todos os pdfs foram gerados com sucesso!!!' ));
  return redirect('/admin/dashboard')->with(['msg'=>'Estamos processando a geração dos pdfs de comissão, avisaremos ao término','class'=>'info']);
});
Route::get('/baixar-pdfs','ComissoesController@gerarPdfs');
//ajax
Route::get('/login','AuthController@index');
Route::group(['prefix'=> 'api', 'middleware' => 'cors'],function(){
  Route::get('ajax/codigo/{codigo}/data/{data}','ApiAjaxController@getCheckCodigo');
  Route::get('ajax/boleto/{num}','ApiAjaxController@getCheckBoleto');

  // Route for Franqueados relationship with Fda
  Route::get('/ajax/fda/{fda}','ApiAjaxController@getFranqueados');
  //Route Edit Products
  Route::get('ajax/produto-details/{idproduto}','ApiAjaxController@getDetailsProduct');
  Route::post('ajax/editProduct/{idproduto}','ApiAjaxController@postEditProduct');

  //Route getCliente for boleto.acqio.co
  Route::get('/v1/getCliente/{c}','ApiAjaxController@getCliente');
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

    Route::get('/orders/list',
      ['as'=>'admin.orders.list.index','uses'=>'OrdensPagamentoController@view']);
    Route::resource('orders','OrdensPagamentoController',['except'=>['create','edit']]);
    //Acessar os pdfs
    Route::get('/orders/{id}/{filename}', function ($id,$filename)
    {
        $path = storage_path().'/'.\App\Models\OrdemPagamento::where('id',$id)->first()->relatorio_pdf;
        // dd($path, !File::exists($path));
        if(!File::exists($path)) abort(404);

        $file = File::get($path);
        $type = File::mimeType($path);
        // dd($file);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    });

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
