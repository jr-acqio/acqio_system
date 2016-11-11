<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Admin</title>
  <link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

  <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

</head>

<body>
  <div id="wrapper">
    @include('layouts.menu')

    <div id="page-wrapper" class="gray-bg">
      @include('layouts.top')
      <div class="wrapper wrapper-content">

        <!-- 2 Row -->
        <div class="row">
          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-danger pull-right">Low value</span> -->
                <h5>Clientes <i class="fa fa-users" aria-hidden="true"></i></h5>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">Clientes ({{ App\Models\Cliente::select('cpf','cnpj')->distinct()->get()->count() }})</h1>
                <!-- <div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div> -->
                <small>Total</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-danger pull-right">Low value</span> -->
                <h5>Pedidos <i class="fa fa-tasks" aria-hidden="true"></i></h5>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">Pedidos ({{ App\Models\Pedidos::all()->count() }})</h1>
                <!-- <div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div> -->
                <small>Total</small>
              </div>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-danger pull-right">Low value</span> -->
                <h5>Vendas Canceladas</h5>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">{{ App\Models\Pedidos::where('status','=','0')->whereDate('data_cancel','=',\Carbon\Carbon::Now()->format('Y-m-d'))->get()->count() }}</h1>
                <!-- <div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div> -->
                <small>Hoje</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-danger pull-right"></span> -->
                <h5>Vendas Aprovadas - {{ \Carbon\Carbon::Now()->format('d/m/Y') }}</h5>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">{{ App\Models\Pedidos::whereDate('created_at','=',\Carbon\Carbon::Now()->format('Y-m-d'))->count() }}</h1>
                <div class="stat-percent font-bold">
                  <a href="http://suporteacqio.esy.es/admin/search/search-vendas?cliente=&produto=&codigo=&boleto=&status=&versao_sis=&data_inicio={{\Carbon\Carbon::Now()->format('Y-m-d')}}&data_final={{\Carbon\Carbon::Now()->format('Y-m-d')}}">
                  Visualizar <i class="fa fa-search" aria-hidden="true"></i>
                </a>
                </div>
                <small>Hoje</small>
              </div>
            </div>
          </div>
        </div>
        <!-- End Row -->

        <div class="row">
          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-success pull-right">Total</span> -->
                <h5>Receita Boletos <i class="fa fa-barcode" aria-hidden="true"></i></h5>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">{{number_format(App\Models\PagamentoBoleto::sum('valor'), 2) }}</h1>
                <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> -->
                <small>Todos Boletos</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-primary pull-right">Total</span> -->
                <h5>Receita Cartão <i class="fa fa-credit-card-alt" aria-hidden="true"></i></h5>
                <div class="stat-percent font-bold @if($tcd < 0) text-danger @else text-success @endif">{{ number_format($tcd, 2, '.', '') }}% <i class="fa @if($tcd < 0) fa-level-down @else fa-level-up @endif"></i></div>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">{{number_format(App\Models\PagamentoCartao::sum('valor_total'), 2) }}</h1>
                <small>Cálculo de Janeiro/2016 à hoje</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="ibox float-e-margins">
              <div class="ibox-title">
                <!-- <span class="label label-primary pull-right">Total</span> -->
                <h5>Pagamentos Aprovados <i class="fa fa-check-square-o" aria-hidden="true"></i></h5>
              </div>
              <div class="ibox-content">
                <h1 class="no-margins">
                  {{ number_format($totalAprovado,2) }}
                </h1>
                <!-- <div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div> -->
                <small>Total</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Row Charts  -->
        <div class="row">
          <div class="col-lg-12">
              <div class="ibox float-e-margins">
                  <div class="ibox-title">
                      <h5>Comparativo de Vendas - <span style="background:rgba(220,220,220,0.5)">Boletos</span> x <span style="background: rgba(26,179,148,0.5);">Cartão</span:</h5>
                  </div>
                  <div class="ibox-content">
                      <div>
                          <canvas id="barChart" height="70" style="height: 100px;"></canvas>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        <!-- End Row Chart -->
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

  <!-- Chart  -->
  <script src="{{ asset('admin/js/plugins/chartJs/Chart.min.js') }}"></script>

  <script>
    $(document).ready(function() {
      $(function () {
        var barData = {
            labels: [
              "Janeiro",
              "Feverereiro",
              "Março",
              "Abril",
              "Maio",
              "Junho",
              "Julho",
              "Agosto",
              "Setembro",
              "Outubro",
              "Novembro",
              "Dezembro"
            ],
            datasets: [
                {
                    label: "Boletos",
                    fillColor: "rgba(220,220,220,0.5)",
                    strokeColor: "rgba(220,220,220,0.8)",
                    highlightFill: "rgba(220,220,220,0.75)",
                    highlightStroke: "rgba(220,220,220,1)",
                    data: [
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','0')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','1')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','2')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','3')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','4')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','5')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','6')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','7')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','8')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','9')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','10')->count() }},
                      {{ App\Models\PagamentoBoleto::join('pagamentos','pagamentos.id','=','pagamentos_boleto.pagamento_id')->whereMonth('created_at','=','11')->count() }},
                    ]
                },
                {
                    label: "Cartão",
                    fillColor: "rgba(26,179,148,0.5)",
                    strokeColor: "rgba(26,179,148,0.8)",
                    highlightFill: "rgba(26,179,148,0.75)",
                    highlightStroke: "rgba(26,179,148,1)",
                    data: [
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','1')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','2')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','3')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','4')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','5')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','6')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','7')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','8')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','9')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','10')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','11')->count() }},
                      {{ App\Models\PagamentoCartao::join('pagamentos','pagamentos.id','=','pagamentos_cartao.pagamento_id')->whereMonth('data','=','12')->whereYear('data','=','2016')->count() }},
                  ]
                }
            ]
        };

        var barOptions = {
            scaleBeginAtZero: true,
            scaleShowGridLines: true,
            scaleGridLineColor: "rgba(0,0,0,.05)",
            scaleGridLineWidth: 1,
            barShowStroke: true,
            barStrokeWidth: 2,
            barValueSpacing: 5,
            barDatasetSpacing: 1,
            responsive: true
        }
        var ctx = document.getElementById("barChart").getContext("2d");
        var myNewChart = new Chart(ctx).Bar(barData, barOptions);
      });
    });
  </script>
</body>
</html>
