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
        <div class="row  border-bottom white-bg page-heading">
          <div class="col-sm-4">
            <h2>Clientes</h2>
            <ol class="breadcrumb">
              <li>
                <a href="/admin/dashboard">Home</a>
              </li>
              <li>
                <a href="/admin/clientes/clientes">Clientes</a>
              </li>
              <li class="active">
                <strong>Histórico de Pedidos</strong>
              </li>
            </ol>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="animated fadeInUp">
            <div class="ibox">
              <div class="ibox-content">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="m-b-md">
                      <a href="#" class="btn btn-white btn-xs pull-right">Editar Cliente</a>
                      <h2>@if($cliente->cpf == null)
                        {{$cliente->razao}}
                        @else
                        {{$cliente->nome}}
                        @endif
                      </h2>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-5">
                    <dl class="dl-horizontal">
                      <dt>Status:</dt> <dd><span class="label" style="color:green;"><b>Active</b></span></dd>
                      <dt>Criado por:</dt> <dd>Alex Smith</dd>
                      <dt>Pedidos:</dt> <dd>  {{ $history->count() }}</dd>
                    </dl>
                  </div>
                  <div class="col-lg-7" id="cluster_info">
                    <dl class="dl-horizontal" >
                      <dt>Ultima atualização:</dt> <dd>{{$cliente->updated_at->format("d/m/Y H:m:s")}}</dd>
                      <dt>Criado:</dt> <dd> 	{{$cliente->created_at->format("d/m/Y H:m:s")}} </dd>
                    </dl>
                  </div>
                </div>

                <div class="row m-t-sm">
                  <div class="col-lg-12">
                    <div class="panel blank-panel">
                      <div class="panel-heading">
                        <div class="panel-options">
                          <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab-1" data-toggle="tab">Histórico de Pedidos</a></li>
                            <!-- <li class=""><a href="#tab-2" data-toggle="tab">Last activity</a></li> -->
                          </ul>
                        </div>
                      </div>
                      <div class="panel-body">

                        <div class="tab-content">
                          <div class="tab-pane active" id="tab-1">
                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Status</th>
                                  <th>Data / Hora</th>
                                  <th>Documento</th>
                                  <th>Cliente</th>
                                  <th>Produtos (qnt)</th>
                                  <th>Valor da Compra R$</th>
                                  <th>Ações</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($history as $hp)
                                <tr>
                                  <td>
                                    @if($hp->status == 1)
                                    <span class="label" style="color:green;"><i class="fa fa-check"></i> <b>Concluído</b></span>
                                    @else
                                    <span class="label label-danger"><i class="fa fa-check"></i> <b>Cancelado</b></span>
                                    @endif
                                  </td>
                                  <td>
                                    {{ $hp->created_at->format("d/m/Y H:m:s") }}
                                  </td>
                                  <td>
                                    @if($cliente->cpf == null)
                                    {{ $cliente->cnpj }}
                                    @else
                                    {{ $cliente->cpf }}
                                    @endif
                                  </td>
                                  <td>
                                    @if($cliente->cpf == null)
                                    {{ $cliente->razao }}
                                    @else
                                    {{ $cliente->nome }}
                                    @endif
                                  </td>
                                  <td>{{ $hp->quantidade }}</td>
                                  <td>R$ {{ number_format($hp->valortotal,2) }}</td>
                                  <td>
                                    <a href="/admin/checagem/view/{{$hp->id}}" class="btn btn-xs btn-default">Ver Detalhes <i class="fa fa-search"></i></a>
                                  </td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>

                          </div>
                          <!-- <div class="tab-pane" id="tab-2">

                          </div> -->
                        </div>

                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
