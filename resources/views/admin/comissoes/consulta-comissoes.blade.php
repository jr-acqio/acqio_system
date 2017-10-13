@extends('layouts.master')

@section('title','Filtrar Comissões')
@push('links')
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
      @if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form class="" action="{{ url('admin/comissoes/filtrar') }}" method="get">

        <div class="row">
          <div class="form-group col-lg-4 {{ $errors->has('tipo') ? ' has-error' : '' }}">
            <label for="">TIPO CLIENTE</label>
            <div class="">
              <input type="radio" required name="tipo" value="fda">Fda
              <input type="radio" required name="tipo" value="franqueado">Franqueado
            </div>
            @if ($errors->has('tipo'))
            <span class="help-block">
              <strong>{{ $errors->first('tipo') }}</strong>
            </span>
            @endif
          </div>

          <div class="form-group col-lg-4 data {{ $errors->has('daterange') ? ' has-error' : '' }}">
            <label>Período de aprovação</label>
            <div class="input-group date">
              <span class="input-group-addon btn btn-primary">
                <i class="fa fa-calendar"></i>
              </span>
              <input class="form-control" type="text" name="daterange" value="01/{{\Carbon\Carbon::now()->format('m/Y')}} - {{\Carbon\Carbon::now()->format('d/m/Y')}}" />
            </div>
          </div>
        </div>

      </div>
      <div class="ibox-footer">
        <button type="submit" class="btn btn-primary"><b>Filtrar</b></button>
      </div>
    </form>
  </div>
</div>

@if(isset($fda))
<div class="animated fadeInRight">
  <div class="ibox-content p-xl">
    <div class="row">
      <div class="col-sm-8 text-left">
        <h4>FDA: ({{ $inputs['cliente'] }}) - {{ $fda->nome_razao }}</h4>
        <p>
          <span><strong>Total de Vendas:</strong> {{ $comissoes->count() }}</span>
        </p>
        <p>
          <span><strong>Total de POS:</strong> {{ $comissoes->sum('totalProdutos') }}</span>
        </p>
        <p>
          <span><strong>Período:</strong> {{ $inputs['daterange'] }}</span><br/>
        </p>
      </div>
      <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-primary imprimir"><i class="fa fa-print"></i> Imprimir Comissão</a>
      </div>
    </div>

    <div class="table-responsive m-t">
      <table class="table invoice-table table-hover">
        <thead>
          <th>Data Cadastro</th>
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
            <td>{{ date('d/m/Y',strtotime($c->data_aprovacao)) }}</td>
            <td>@if($c->franqueadoid == null) Primeira Compra @else {{ strtoupper($c->franqueadoid) }} @endif</td>
            <td>{{ strtoupper($c->nome_cliente) }}</td>
            <td>
              {{ \App\Models\Comissoes::find($c->comissaoid)->produtos->implode('descricao',', ') }}
            </td>
            <td class="text-center">{{ $c->totalProdutos }}</td>
            <td>
              @if($c->franqueadoid == null)
                R$ {{ number_format($c->totalInstalacao + $c->totalVenda,2) }}
              @else
                R$ {{ number_format($c->totalInstalacao,2) }}
              @endif
            </td>
          </tr>

          <?php
                  if ($c->franqueadoid == null){
                      $sum += $c->totalInstalacao + $c->totalVenda;
                  }else{
                      $sum += $c->totalInstalacao;
                  }
          ?>
          @endforeach
        </tbody>
      </table>
    </div><!-- /table-responsive -->

    <table class="table invoice-total">
      <tbody>
        <tr>
          <td><strong>TOTAL :</strong></td>
          <td>R$ {{ number_format($sum,2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@endif
@if(isset($fr))
<div class="animated fadeInRight">
  <div class="ibox-content p-xl">
    <div class="row">
      <div class="col-sm-6">
        <h4>FRANQUEADO: ({{ $inputs['cliente'] }}) - {{ $fr->nome_razao }}</h4>
        <p>
          <span><strong>Total de Vendas:</strong> {{ $comissoes->count() }}</span>
        </p>
        <p>
          <span><strong>Total de POS:</strong> {{ $comissoes->sum('totalProdutos') }}</span>
        </p>
        <p>
          <span><strong>Período:</strong> {{ $inputs['daterange'] }}</span><br/>
        </p>
      </div>
      <div class="col-sm-6 text-right">
        <!-- <h4>Invoice No.</h4>
        <h4 class="text-navy">INV-000567F7-00</h4> -->
        <p>
          <span><strong>Período:</strong> {{ $inputs['daterange'] }}</span><br/>
        </p>
      </div>
    </div>

    <div class="table-responsive m-t">
      <table class="table invoice-table table-hover">
        <thead>
          <th>Data Cadastro</th>
          <th>Data Aprovação</th>
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
            <td>{{ date('d/m/Y',strtotime($c->data_aprovacao)) }}</td>
            <!-- <td>{{ strtoupper(DB::table('franqueados')->where('id',$c->franqueadoid)->value('nome_razao')) }}</td> -->
            <td>{{ strtoupper($c->nome_cliente) }}</td>
            <td>
              {{ \App\Models\Comissoes::find($c->comissaoid)->produtos->implode('descricao',', ') }}
            </td>
            <td class="text-center">{{ \App\Models\Comissoes::find($c->comissaoid)->produtos->count() }}</td>
            <td>R$ {{ number_format($c->totalVenda,2) }}</td>
          </tr>
          <?php $sum += $c->totalVenda; ?>
          @endforeach
        </tbody>
      </table>
    </div><!-- /table-responsive -->

    <table class="table invoice-total">
      <tbody>
        <tr>
          <td><strong>TOTAL :</strong></td>
          <td>R$ {{ number_format($sum,2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endif


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
  // Print
  $('.imprimir').click(function(){
    window.print();
  });

  $('input[name=tipo]').on('click',function(){
    if($(this).val() == 'fda'){
      $('#franqueado').remove();
      $('#fda').remove();
      $('.data').parent('div').append(templateFda);
      // configChosen();
    }else{
      $('#fda').remove();
      $('#franqueado').remove();
      $('.data').parent('div').append(templateFranq);
    }
    configChosen();
  });
});

$('input[name="daterange"]').daterangepicker({
  "autoApply": true,
  "opens": "left",
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
</script>
<script type="text/javascript">
var templateFda = '<div class="form-group col-lg-4" id="fda">'+
'<label>SELECIONE UM FDA</label>'+
'<select class="form-control chosen-select" name="cliente">'+
'<option value="">SELECIONE UMA OPÇÃO</option>'+
'@foreach($fdas as $f)'+
'<option value="{{$f->fdaid}}">{{strtoupper($f->fdaid)}}</option>'+
'@endforeach'+
'</select>'+
'</div>';
var templateFranq = '<div class="form-group col-lg-4" id="franqueado">'+
'<label>SELECIONE UM FRANQUEADO</label>'+
'<select class="form-control chosen-select" name="cliente">'+
'<option value="">SELECIONE UMA OPÇÃO</option>'+
'@foreach($franqueados as $fr)'+
'<option value="{{$fr->franqueadoid}}">{{strtoupper($fr->franqueadoid)}}</option>'+
'@endforeach'+
'</select>'+
'</div>';

</script>
@endpush
