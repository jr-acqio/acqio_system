@extends('layouts.master')

@section('title','Filtrar Comissões')
@push('links')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

<link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

<!-- DataTables -->
<link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">

<!-- Chosen Style -->
<link href="{{ asset('admin/css/plugins/chosen/chosen.css') }}" rel="stylesheet">

<!-- DatePicker -->
<link rel="stylesheet" href="{{ asset('admin/css/plugins/colorpicker/bootstrap-colorpicker.min.css') }} " media="screen" title="no title">
<!-- DatePicker Style -->
<!-- <link rel="stylesheet" href="{{ asset('admin/css/plugins/datapicker/datepicker3.css') }}" media="screen" title="no title"> -->
<link href="{{ asset('admin/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/select2/select2.min.css') }} " rel="stylesheet">

<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
 <script>
  tinymce.init({
    selector:"textarea[name='mensagem']",
    plugins: [
      'advlist autolink lists link image charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'insertdatetime media table contextmenu paste code'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  });
 </script>
@endpush

@section('content')

<div class="row wrapper border-bottom white-bg page-heading" style="margin: 1px 1px;">
  <div class="col-lg-12">
    <h2>Comissão</h2>
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
</div>

<div class="row" style="margin: 1px 1px;">
  <div class="ibox float-e-margins">
    <div class="ibox-title">
      <h5>Filtrar Comissoes</h5>
    </div>
    <div class="ibox-content">
      @if(isset($msg))
        <div class="alert alert-{{$class}}">
          <b>{{ $msg }}</b>
        </div>
      @endif
      @if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form class="" action="{{ url('admin/comissoes/list/all') }}" method="get">
        <!-- Row -->
        <div class="row">
          <div class="form-group col-lg-4 data {{ $errors->has('daterange') ? ' has-error' : '' }}">
            <label>Período de aprovação</label>
            <div class="input-group date">
              <span class="input-group-addon btn btn-primary">
                <i class="fa fa-calendar"></i>
              </span>
              <input class="form-control" type="text" name="daterange" value="01/{{\Carbon\Carbon::now()->format('m/Y')}} - {{\Carbon\Carbon::now()->format('d/m/Y')}}" />
            </div>
          </div>
          <div class="form-group col-lg-8">
            <label>To: </label>
            <input type="text" class="form-control" name="to" value="">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-lg-12">
            <label>Cc:</label>
            <select class="selectcc form-control" name="copias[]" multiple="">
              <option value="luana.monteiro@esfera5.com.br">Luana Monteiro</option>
              <option value="leandro.xavier@esfera5.com.br">Leandro Xavier</option>
              <option value="stefano.andrei@esfera5.com.br">Stefano Andrei</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-lg-12">
            <label>Mensagem:</label>
            <textarea name="mensagem" class="form-control" rows="8" cols="40"></textarea>
          </div>
        </div>
        <!-- Finish Row -->
      </div>
      <div class="ibox-footer">
        <button type="submit" class="btn btn-primary"><b>Filtrar</b></button>
        <button type="submit" name="mail" class="btn btn-primary"><b>Filtrar e enviar email <i class="fa fa-envelope"></i></b></button>
      </div>
    </form>
  </div>
</div>

@if(isset($comissoes))
<div class="row">
  <div class="col-lg-12">
    <div class="animated fadeInUp">
      <div class="ibox">
        <div class="ibox-content">
          <div class="row">
            <div class="col-lg-4">
              <h3>Quantidade de Fda: {{ $comissoes->count() }}</h3>
              <p>
                Valor Total: R$ {{ number_format($comissoes->sum('valor'),2,',', '.') }}
              </p>
            </div>
            <div class="col-lg-4">
              <h3>Quantidade de Franqueados: {{ $comissoes_fr->count() }}</h3>
              <p>
                Valor Total: R$ {{ number_format($comissoes_fr->sum('valor'),2,',', '.') }}
              </p>
            </div>
            <div class="col-lg-4 text-right">
              <h3>Período: {{ $inputs['daterange'] }}</h3>
            </div>
          </div>
          <div class="row m-t-sm">
            <div class="col-lg-12">
              <div class="panel blank-panel">
                <div class="panel-heading">
                  <div class="panel-options">
                    <ul class="nav nav-tabs">
                      <li class="active"><a href="#tab-1" data-toggle="tab">COMISSÕES DE FDA</a></li>
                      <li class=""><a href="#tab-2" data-toggle="tab">COMISSÕES DE FRANQUEADO</a></li>
                    </ul>
                  </div>
                </div>
                <div class="panel-body">

                  <div class="tab-content">
                    <div class="tab-pane active" id="tab-1">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Identificador Fda</th>
                            <th>Nome Fda</th>
                            <th>Total de Vendas</th>
                            <th>Total de POS</th>
                            <th>Valor à pagar</th>
                            <th>Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($comissoes as $c)
                            <tr>
                              <td>{{ $c->fdaid }}</td>
                              <td>
                                {{ strtoupper($c->nome_razao) }}
                                <!-- <span class="label" style="color:green;"><i class="fa fa-check"></i> <b>Concluído</b></span> -->
                              </td>
                              <td>
                                {{
                                  \App\Models\Fda::join('comissoes as c','c.fdaid','=','fdas.id')
                                  ->whereDate('c.data_aprovacao','<=',$datafinal)
                                  ->whereDate('c.data_aprovacao','>=',$datainicial)
                                  ->where('c.fdaid',$c->id)
                                  ->get()->count()
                                }}
                              </td>
                              <td>{{ $c->totalProdutos }}</td>
                              <td>
                                <?php $valor_primeiracompra = \App\Models\Fda::join('comissoes as c','c.fdaid','=','fdas.id')
                                ->join('comissoes_produto as cp', 'cp.comissaoid', '=', 'c.id')
                                ->groupBy('c.id')
                                ->whereDate('c.data_aprovacao','<=',$datafinal)
                                ->whereDate('c.data_aprovacao','>=',$datainicial)
                                ->where('c.fdaid',$c->id)
                                ->whereNull('c.franqueadoid')
                                ->select(\DB::raw('SUM(cp.tx_venda) as valor'))
                                ->first() ?>
                                R$ {{ number_format($c->valor + $valor_primeiracompra['valor'],2,',', '.') }}
                              </td>
                              <td>
                                <a href="/admin/comissoes/filtrar?tipo=fda&daterange={{$inputs['daterange']}}&cliente={{$c->fdaid}}" class="btn btn-xs btn-default"><b>Detalhes</b></a>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>

                    </div>
                    <div class="tab-pane" id="tab-2">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Identificador Franqueado</th>
                            <th>Nome Franqueado</th>
                            <th>Total de Vendas</th>
                            <th>Total de POS</th>
                            <th>Total Comissão</th>
                            <th>Royalties</th>
                            <th>Valor Liq. à pagar</th>
                            <th>Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($comissoes_fr as $c)
                            <tr>
                              <td>{{ $c->franqueadoid }}</td>
                              <td>
                                {{ strtoupper($c->nome_razao) }}
                                <!-- <span class="label" style="color:green;"><i class="fa fa-check"></i> <b>Concluído</b></span> -->
                              </td>
                              <td>
                                {{
                                  \App\Models\Franqueado::join('comissoes as c','c.franqueadoid','=','franqueados.id')
                                  ->whereDate('c.data_aprovacao','<=',$datafinal)
                                  ->whereDate('c.data_aprovacao','>=',$datainicial)
                                  ->where('c.franqueadoid',$c->id)
                                  ->get()->count()
                                }}
                              </td>
                              <td>{{ $c->totalProdutos }}</td>
                              <td>R$ {{ number_format($c->valor,2,',', '.') }}</td>
                              <?php $valor = \App\Models\Royalties::where('franqueadoid',$c->id)->where('descontado','!=','s')->get();
                                ?>
                              <td style="color: @if($valor->sum('valor_original') + $valor->sum('cheques_devolvidos') > 0) red @endif;">
                                <?php echo 'R$ '.number_format($valor->sum('valor_original') + $valor->sum('cheques_devolvidos'), 2,',','.'); ?>
                              </td>
                              <td style="color: @if($c->valor - ($valor->sum('valor_original') + $valor->sum('cheques_devolvidos')) < 0) red @endif">
                                <?php echo number_format($c->valor - ($valor->sum('valor_original') + $valor->sum('cheques_devolvidos')),2,',','.') ?>
                              </td>
                              <td>
                                <a href="/admin/comissoes/filtrar?tipo=franqueado&daterange={{$inputs['daterange']}}&cliente={{$c->franqueadoid}}" class="btn btn-xs btn-default"><b>Detalhes</b></a>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
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

@endif
<!-- </div> -->



@endsection


@push('scripts')
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

<!-- Chosen -->
<script src="{{ asset('admin/js/plugins/chosen/chosen.jquery.js') }}"></script>


<!-- Date range use moment.js same as full calendar plugin -->
<script src="{{ asset('admin/js/plugins/fullcalendar/moment.min.js') }}"></script>

<script src="{{ asset('admin/js/plugins/daterangepicker/daterangepicker.js') }}" charset="utf-8"></script>
<!-- Select2 -->
<script src="{{ asset('admin/js/plugins/select2/select2.full.min.js') }}"></script>


<script type="text/javascript">
  $(".selectcc").select2({
    tags: true,
    // tokenSeparators: [',', ' ']
  })
</script>
<!-- Chosen Init -->
<script type="text/javascript">
function configChosen(){
  var config = {
    '.chosen-select'           : {},
    '.chosen-select-deselect'  : {allow_single_deselect:true},
    '.chosen-select-no-single' : {disable_search_threshold:10},
    '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
    '.chosen-select-width'     : {width:"100%"}
  }
  for (var selector in config) {
    $(selector).chosen(config[selector]);
  }
}
</script>

<script type="text/javascript">
$(document).ready(function(){
  $('input[name="daterange"]').daterangepicker({
    "autoApply": true,
    "opens": "right",
    "language": "pt-BR",
    "format": "DD/MM/YYYY",
    "locale": {
      "format": "DD/MM/YYYY",
      "separator": " / ",
      "applyLabel": "Aplicar",
      "cancelLabel": "Cancelar",
      "fromLabel": "De",
      "toLabel": "Até",
      "customRangeLabel": "Customizado",
      "weekLabel": "W",
      "daysOfWeek": [
        "Dom",
        "Seg",
        "Ter",
        "Qua",
        "Qui",
        "Sex",
        "Sab"
      ],
      "monthNames": [
        "Janeiro",
        "Fevereiro",
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
      "firstDay": 1
    },
    "startDate": "01/{{\Carbon\Carbon::now()->format('m/Y')}}",
    "endDate": "{{\Carbon\Carbon::now()->format('d/m/Y')}}",
    "ranges": {
      'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
      'Últimos 60 dias': [moment().subtract(59, 'days'), moment()],
      'Esse Mês': [moment().startOf('month'), moment().endOf('month')],
      //  'Último Mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  });
});
</script>
@endpush
