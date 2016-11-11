<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Clientes</title>
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
                  <!-- <h2>
                    2,160 results found for: <span class="text-navy">“Admin Theme”</span>
                  </h2> -->

                  <div class="search-form">
                    <form action="{{ url::route('search.cliente') }}" method="get">
                      <div class="input-group">
                        <input type="text" placeholder="PESQUISE PELO NOME OU DOCUMENTO" name="search" class="form-control input-lg">
                        <div class="input-group-btn">
                          <button class="btn btn-lg btn-primary" type="submit">
                            Buscar
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <br><br>
                    @if(isset($clientes))
                    @foreach ($clientes->chunk(4) as $chunk)
                        <div class="row">
                            @foreach ($chunk as $c)
                            <div class="col-lg-3">
                                <div class="contact-box center-version">
                                    <a href="#">
                                        <img alt="image" class="img-circle" src="http://bazyol.com.tr/img/icon-user.png">
                                        <h3 class="m-b-xs"><strong>@if($c->nome == null) {{$c->razao}} @else {{$c->nome}} @endif</strong></h3>
                                    </a>
                                    <div class="contact-box-footer">
                                      <div class="m-t-xs btn-group">
                                          <a class="btn btn-xs btn-default" href="/admin/clientes/historico-pedidos?cliente-id={{encrypt($c->id)}}"><i class="fa fa-list"></i> Lista de Pedidos  </a>
                                          <a class="btn btn-xs btn-default"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar </a>
                                          <!-- <a class="btn btn-xs btn-default"><i class="fa fa-envelope"></i> Email</a>
                                          <a class="btn btn-xs btn-default"><i class="fa fa-user-plus"></i> Follow</a> -->
                                      </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="text-center">
                      {!! $clientes->appends(['search' => Request::input('search')])->render() !!}
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

  <!-- Flot -->
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.spline.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.resize.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.pie.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.symbol.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/flot/jquery.flot.time.js') }}"></script>

  <!-- Peity -->
  <script src="{{ asset('admin/js/plugins/peity/jquery.peity.min.js') }}"></script>
  <script src="{{ asset('admin/js/demo/peity-demo.js') }}"></script>

  <!-- Custom and plugin javascript -->
  <script src="{{ asset('admin/js/inspinia.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

  <!-- jQuery UI -->
  <script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

  <!-- Jvectormap -->
  <script src="{{ asset('admin/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

  <!-- EayPIE -->
  <script src="{{ asset('admin/js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>

  <!-- Sparkline -->
  <script src="{{ asset('admin/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

  <!-- Sparkline demo data  -->
  <script src="{{ asset('admin/js/demo/sparkline-demo.js') }}"></script>

  <!-- DataTable -->
  <script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}" charset="utf-8"></script>

</body>
</html>
