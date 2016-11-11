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

  <!-- DatePicker -->
  <link rel="stylesheet" href="{{ asset('admin/css/plugins/colorpicker/bootstrap-colorpicker.min.css') }} " media="screen" title="no title">
  <!-- DatePicker Style -->
  <link rel="stylesheet" href="{{ asset('admin/css/plugins/datapicker/datepicker3.css') }}" media="screen" title="no title">
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
          <li>Nº Transações: <b>{{ session('resumoUpload')['contador'] }}</b></li>
          <li>Nº Transações Canceladas: <b>{{ session('resumoUpload')['canceladas'] }}</b></li>
          <li>Nº Transações Concluidas: <b>{{ session('resumoUpload')['concluidas'] }}</b></li>
          <li>Nº Transações Novas: <b>{{ session('resumoUpload')['novas'] }}</b></li>
        </div>
        @endif
        @if(session('vendasCanceladas'))
        <div class="alert alert-danger">
          <b>Algumas vendas foram canceladas:</b><br><br>
          @foreach(session('vendasCanceladas') as $v)
          <b>Cliente: {{ $v['cliente'] }}<br>
            Codigo: {{$v['codigo']}},<br>
            Data: {{ $v['data'] }},<br>
            <a href="/admin/checagem/view/{{ $v['pedido'] }}" target="_blank">Venda {{$v['pedido']}}</a>
            <i class="fa fa-hand-o-left" aria-hidden="true"></i>
          </b>
          <br>
          <tr></tr>
          @endforeach
        </div>
        @endif

        <!-- <div class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
        <span class="sr-only">45% Complete</span>
      </div>
    </div> -->
    <div class="ibox float-e-margins">
      <div class="ibox-title">
        <b>Importação de transações</b>
      </div>
      <div class="ibox-content">
        <div class="row">
          <div class="col-lg-12">
            <form class="" action="{{ url::route('imports/upload-transacoes') }}" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="form-group">
                <label for="">Arquivo de importação</label>
                <input type="file" name="arquivo" value="">
              </div>
              <div class="form-group">
                <button type="submit" name="button" class="btn btn-primary"><b>ENVIAR <i class="fa fa-check"></i></b></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="ibox float-e-margins">
      <div class="ibox-title">
        <b>Filtragem de Transações</b>
      </div>
      <div class="ibox-content">
        <div class="row">
          <div class="col-lg-12">
            <form class="" action="{{ url::route('search.transacoes') }}" method="get">
              <div class="row">
                <div class="form-group col-lg-3">
                  <label>Código</label>
                  <input type="text" class="form-control" name="codigo" placeholder="Código de Autorização">
                </div>
                <div class="form-group col-lg-3">
                  <label for="">Status</label>
                  <select class="form-control" name="status">
                    <option value="">Selecione um Status</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Aprovada">Aprovada</option>
                    <option value="Concluida">Concluída</option>
                    <option value="Cancelada">Cancelada</option>
                    <option value="Rejeitada">Rejeitada</option>
                    <option value="Negada">Negada</option>
                  </select>
                </div>
                <div class="form-group col-lg-3">
                  <label>Data Inicio</label>
                  <div class="input-group date">
                      <span class="input-group-addon btn btn-primary">
                        <i class="fa fa-calendar"></i>
                      </span>
                      <input type="text" name="data_inicio" class="form-control" value="" placeholder="Data Inicio">
                  </div>
                </div>
                <div class="form-group col-lg-3">
                  <label>Data Final</label>
                  <div class="input-group date">
                      <span class="input-group-addon btn btn-primary">
                        <i class="fa fa-calendar"></i>
                      </span>
                      <input type="text" name="data_final" class="form-control" value="" placeholder="Data Final">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-lg-12">
                  <button type="submit" class="btn btn-primary"><b>ENVIAR <i class="fa fa-check"></i></b></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Start DataTable -->
    <div class="ibox float-e-margins">
      <div class="ibox-title">
        <div class="row">
          @if(isset($transactions))
          <div class="col-lg-3">
            Total de Transações ({{ $transactions->count() }})
          </div>
          <div class="col-lg-3">
            Pendentes ({{ $transactions->where('status','Pendente')->count() }})
          </div>
          <div class="col-lg-3">
            Concluida ({{ $transactions->where('status','Concluida')->count() }})
          </div>
          <div class="col-lg-3">
            Cancelada ({{ $transactions->where('status','Cancelada')->count() }})
          </div>
          @endif
        </div>
      </div>
      <div class="ibox-content">
        <div class="row">
          <!-- Start table -->
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-highlight" id="tableTransactions"
            data-provide="datatable"
            data-display-rows="25"
            data-info="false"
            data-search="false"
            data-length-change="false"
            data-paginate="true">
            <thead>
              <td>Data / Hora</td>
              <td>Autorização</td>
              <td>Status</td>
              <td>Estabelecimento</td>
              <td>Valor</td>
              <td>Valor Líquido</td>
              <td>Faturamento</td>
            </thead>
            <tbody>
              @if(isset($transactions))
              @foreach($transactions as $t)
              <tr>
                <td>{{ implode('/', array_reverse(explode('-',$t->data ))) }} - {{ $t->hora }}</td>
                <td>{{ $t->codigo }}</td>
                <td>{{ $t->status }}</td>
                <td>{{ $t->loja }}</td>
                <td>R$ {{ number_format($t->valor_total, 2) }}</td>
                <td>R$ {{ number_format($t->valor_total_liquido, 2) }}</td>
                <td>R$ {{ number_format($t->faturamento, 2) }}</td>
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

<!-- Peity -->
<script src="{{ asset('admin/js/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('admin/js/demo/peity-demo.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('admin/js/inspinia.js') }}"></script>
<script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- DataTable -->
<script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}" charset="utf-8"></script>

<!-- DatePicker -->
<script src="{{ asset('admin/js/plugins/datapicker/bootstrap-datepicker.js') }}" charset="utf-8"></script>
<!-- DatePicker BR -->
<script src="https://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR.min.js" charset="utf-8"></script>

<script>
$(document).ready(function() {
  $('#tableTransactions').dataTable({
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
  $('.input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: "dd/mm/yyyy",
            language: "pt-BR"
        });
});
</script>
</body>
</html>
