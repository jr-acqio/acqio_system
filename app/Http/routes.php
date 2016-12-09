<?php
use Vinkla\Pusher\Facades\Pusher;
Route::get('/enviar-email','MailController@sendMail');

// Route::get('/download-pdf-fda',function(){
//   // Criar diretório do mês e ano referente as comissões no diretório : storage/relatorio-comissao/fdas/mês_Ano
//
// });
Route::get('/teste',function(){
  \App\User::create([
    'name'=>"Jose",
    'email'=>'junr@hotmail.com',
    'password'=> Hash::make('123456')
  ]);
});
Route::get('/baixar-pdfs','ComissoesController@gerarPdfs');
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
