<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Franqueados</title>
  <link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

  <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

  <!-- DataTables -->
  <link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
</head>

<body>
  <div id="wrapper">
    @include('layouts.menu')

    <div id="page-wrapper" class="gray-bg">
      @include('layouts.top')
      <div class="wrapper wrapper-content">
        @if(session('msg'))
        <div class="alert alert-{{ session('class') }}">
          <b>{{ session('msg') }}</b>
        </div>
        @endif

        <div class="animated fadeInRight">
          <div class="row">
            <div class="col-lg-12">
              <div class="ibox float-e-margins">
                <div class="ibox-content">
                  @if(isset($clientes))
                  <h2>
                    {{$clientes->total() }} resultados encontrados para: <span class="text-navy">“{{request('search')}}”</span>
                  </h2>
                  @endif

                  <div class="search-form">
                    <form method="get">
                      <div class="input-group">
                        <input type="text" placeholder="PESQUISE PELO NOME, DOCUMENTO, ID OU EMAIL" name="search" class="form-control input-lg" value="{{request('search')}}">
                        <div class="input-group-btn">
                          <button class="btn btn-lg btn-primary" type="submit">
                            Buscar
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <br><br>
                    @if(isset($clientes) && $clientes->count() > 0)
                    <!-- <h2>Exibindo {{ $clientes->firstItem() }} de {{ $clientes->lastItem()}}</h2> -->
                    @foreach ($clientes->chunk(4) as $chunk)
                        <div class="row">
                            @foreach ($chunk as $c)
                            <div class="col-lg-3">
                                <div class="contact-box center-version">
                                    <a href="#">
                                        <img alt="image" class="img-circle" src="http://bazyol.com.tr/img/icon-user.png">
                                        <h3 class="m-b-xs"><strong>{{strtoupper($c->nome_razao)}}</strong></h3>
                                    </a>
                                    <div class="contact-box-footer">
                                        <div class="m-t-xs btn-group">
                                            <a href="/admin/franqueado/historico-pedidos/{{encrypt($c->id)}}" class="btn btn-xs btn-default"><i class="fa fa-list"></i> Histórico de Vendas </a>
                                            <a class="btn btn-xs btn-default"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="text-center">
                      {!! $clientes->appends(['search' => request('search')])->render() !!}
                    </div>
                    @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="ibox float-e-margins">

        </div>
      </div>
      @include('layouts.footer')
    </div>
    @include('layouts.right-side-bar')
  </div>

  <!-- Mainly scripts -->
  <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
  <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

  <!-- Custom and plugin javascript -->
  <script src="{{ asset('admin/js/inspinia.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

  <!-- jQuery UI -->
  <script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>


</body>
</html>
