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

@endpush

@section('content')

<div class="row wrapper border-bottom white-bg page-heading" style="margin: 1px 1px;">
  <div class="col-lg-12">
    <h2>Royalties</h2>
    <ol class="breadcrumb">
      <li>
        <a href="{{ url('/admin/dashboard') }}">Home</a>
      </li>
      <li>
        <a href="{{ url('/admin/comissoes') }}">Royalties</a>
      </li>
      <li class="active">
        <strong>Visualizar</strong>
      </li>
    </ol>
  </div>
</div>

<div class="ibox-content">
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTables-royalties">
      <thead>
        <!-- <th>#</th> -->
        <th>Data Vencimento</th>
        <th>Cliente</th>
        <th>Franquia / Loc</th>
        <th>Valor Original</th>
        <th>Cheques Devolvidos</th>
        <th>Franqueado</th>
        <th>Descontado ?</th>
        <th>Ações</th>
      </thead>
      <tbody>
        @foreach($royalties as $r)
        <tr>
          <!-- <td>{{ $r->id }}</td> -->
          <td>{{ date('d-m-Y',strtotime($r->data_vencimento)) }}</td>
          <td>{{ $r->cliente }}</td>
          <td>{{ $r->franquia_loc }}</td>
          <td>R$ {{ number_format($r->valor_original,2,',','.') }}</td>
          <td>R$ {{ number_format($r->cheques_devolvidos,2,',','.') }}</td>
          <td>{{ $r->franqueadoid }}</td>
          <td>{{ strtoupper($r->descontado) }}</td>
          <td>
            {{ Form::open(['method' => 'DELETE', 'route' => ['admin.royalties.destroy', $r->id]]) }}
                <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
            {{ Form::close() }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>


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

<script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}"></script>

<script type="text/javascript">
$('.dataTables-royalties').DataTable({
  "order": [[ 1, "asc" ]],
  dom: '<"html5buttons"B>lTfgitp',
  buttons: [
      { extend: 'copy'},
      {extend: 'csv'},
      {extend: 'excel', title: 'ExampleFile'},
      {extend: 'pdf', title: 'ExampleFile'},

      {extend: 'print',
       customize: function (win){
              $(win.document.body).addClass('white-bg');
              $(win.document.body).css('font-size', '10px');

              $(win.document.body).find('table')
                      .addClass('compact')
                      .css('font-size', 'inherit');
      }
    }
  ]
});


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

@endpush
