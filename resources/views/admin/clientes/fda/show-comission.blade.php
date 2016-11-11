<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Comissões</title>
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
        <div class="row border-bottom white-bg page-heading">
          <div class="col-lg-8">
            <h2>Comissão - {{ $fda->nome_razao }}</h2>
            <ol class="breadcrumb">
              <li>
                <a href="{{ url('/admin/dashboard') }}">Home</a>
              </li>
              <li>
                <a href="{{ url('/admin/comissoes') }}">Comissões</a>
              </li>
              <li class="active">
                <strong>Comissão</strong>
              </li>
            </ol>
          </div>
          <div class="col-lg-4">
            <div class="title-action">
              <a target="_blank" href="{{ url('/admin/comissoes/historico-pedidos/print/type=fda&cliente='.$fda->id) }}" class="btn btn-primary"><i class="fa fa-print"></i> Imprimir Comissão</a>
            </div>
          </div>
        </div>

        <br>
        <div class="row">
          <div class="row">
            <div class="col-lg-12">
              <div class="animated fadeInRight">
                <div class="ibox-content p-xl">
                  <div class="row">
                    <div class="col-sm-12 text-right">
                      <!-- <h4>Invoice No.</h4>
                      <h4 class="text-navy">INV-000567F7-00</h4> -->
                      <p>
                        <span><strong>Data Inicial:</strong> Marh 18, 2014</span><br/>
                        <span><strong>Data Final:</strong> March 24, 2014</span>
                      </p>
                    </div>
                  </div>

                  <div class="table-responsive m-t">
                    <table class="table invoice-table">
                      <thead>
                          <th>Data Aprovação</th>
                          <th>Franqueado</th>
                          <th>Cliente</th>
                          <th>Produto</th>
                          <th>Quantidade</th>
                          <th>Valor R$</th>
                      </thead>
                      <tbody>
                        <?php $sum = 0; ?>
                        @foreach($comissoes as $c)
                        <tr>
                          <td>{{ date('d/m/Y', strtotime($c->data_cadastro)) }}</td>
                          <td>{{ strtoupper($c->franqueadoid) }}</td>
                          <td>{{ strtoupper($c->nome_cliente) }}</td>
                          <td>
                            {{ \App\Models\Comissoes::find($c->comissaoid)->produtos->implode('descricao',', ') }}
                          </td>
                          <td class="text-center">{{ \App\Models\Comissoes::find($c->comissaoid)->produtos->count() }}</td>
                          <td>R$ {{ number_format($c->totalInstalacao,2) }}</td>
                        </tr>

                        <?php $sum += $c->totalInstalacao; ?>
                        @endforeach
                        </tbody>
                      </table>
                    </div><!-- /table-responsive -->

                    <table class="table invoice-total">
                      <tbody>
                        <!-- <tr>
                        <td><strong>Sub Total :</strong></td>
                        <td>$1026.00</td>
                      </tr>
                      <tr>
                      <td><strong>TAX :</strong></td>
                      <td>$235.98</td>
                    </tr> -->
                    <tr>
                      <td><strong>TOTAL :</strong></td>
                      <td>R$ {{ number_format($sum,2) }}</td>
                    </tr>
                  </tbody>
                </table>
                <!-- <div class="text-right">
                <button class="btn btn-primary"><i class="fa fa-dollar"></i> Make A Payment</button>
              </div> -->

              <!-- <div class="well m-t"><strong>Comments</strong>
              It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
            </div> -->
          </div>
        </div>
      </div>
    </div>
    <br>
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
<script type="text/javascript">
  $(document).ready(function(){
    $(".print").click(function(){
        window.print();
    });
  });
</script>

</body>
</html>
