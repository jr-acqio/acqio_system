<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Importação de pagamentos</title>
  <link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

  <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

  <!-- DataTables -->
  <link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
  <!-- <link rel="stylesheet" href="{{ asset('admin/css/dataTable.css') }}" media="screen" title="no title" charset="utf-8"> -->
</head>

<body>
  <div id="wrapper">
    @include('layouts.menu')

    <div id="page-wrapper" class="gray-bg">
      @include('layouts.top')
      <div class="wrapper wrapper-content">
        @if(session('resumoUpload'))
          @if(session('msg'))
            <div class="alert alert-{{ session('class') }}">
              <b>{{ session('msg') }}</b>
            </div>
          @endif
          <div class="alert alert-info">
            <b>Resumo do Upload</b>
              <li>Total de boletos no arquivo: <b>{{ session('resumoUpload')['contador'] }}</b></li>
              {{--<li>Boletos pagos encontrados (BRADESCO): <b>{{ session('resumoUpload')['novasBradesco'] }}</b></li>--}}
              {{--<li>Boletos pagos encontrados (BBrasil): <b>{{ session('resumoUpload')['novasBBrasil'] }}</b></li>--}}
          </div>
        @endif
        <!-- <div class="progress">
          <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
            <span class="sr-only">45% Complete</span>
          </div>
        </div> -->
        <form class="" action="{{ url::route('imports/upload-boleto') }}" method="post" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="form-group">
            <label for="">Arquivo de importação</label>
            <input type="file" name="arquivo" value="">
          </div>
          <div class="form-group">
            <button type="submit" name="button" class="btn btn-primary">ENVIAR</button>
          </div>
        </form>

        <!-- Start DataTable -->
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <div class="row">
              <div class="col-lg-6">
                    Total de Boletos ({{ $boletos->count() }})
              </div>
              <div class="col-lg-6">
                Receita R$ {{ number_format($boletos->sum('valor'), 2) }}
              </div>
            </div>
          </div>
          <div class="ibox-content">
            <div class="row">
              <!-- Start table -->
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-highlight" id="tableBoletos"
                 data-provide="datatable"
                 data-display-rows="25"
                 data-info="false"
                 data-search="false"
                 data-length-change="false"
                 data-paginate="true">
                  <thead>
                    <td>Registrado em:</td>
                    <td>Nosso Número</td>
                    <td>Valor do Boleto</td>
                    <td>Data de Pagamento</td>
                    <td>Situação</td>
                  </thead>
                  <tbody>
                    @if($boletos->count() > 0)
                      @foreach($boletos as $b)
                        <tr>
                          <td>{{ App\Models\Pagamento::where('id',$b->pagamento_id)->value('created_at') }}</td>
                          <td>{{ $b->numero }}</td>
                          <td>R$ {{ number_format($b->valor, 2) }}</td>
                          <td>@if($b->data != '0000-00-00') {{date('d/m/Y',strtotime($b->data))}} @endif</td>
                          <td>{{ $b->situacao }}</td>
                        </tr>
                      @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
              <!-- End Table -->
            </div>
          </div>
        </div>
        <!-- End dataTable -->
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

  <!-- DataTable -->
  <script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}" charset="utf-8"></script>
  <script>
  $(document).ready(function() {
    $('#tableBoletos').dataTable({
      "oLanguage": {
            "sProcessing": "Processando...",
            "sLengthMenu": "_MENU_ Registros",
            "sZeroRecords": "Não foram encontrados resultados",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Nenhum registro encontrado",
            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            "sInfoPostFix": "",
            "sSearch": "Filtrar:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Próximo",
                "sLast": "Último"
            }
        }
    });

  });
</script>
</body>
</html>
