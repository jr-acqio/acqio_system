@extends('layouts.master')

@section('title','Cadastrar FRANQUEADO')
@push('links')
<link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

<link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

<!-- DataTables -->
<link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush


@section('content')
<div class="ibox float-e-margins">
  <div class="ibox-title">
    <h5>Importar CSV de FRANQUEADOS</h5>
  </div>
  <div class="ibox-content">
    <form action="{{ route('admin.franqueado.store') }}" method="post" enctype="multipart/form-data">
      {{ csrf_field() }}
      <div class="form-group">
        <label>Arquivo</label>
        <input type="file" name="arquivo" value="">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary"><b>ENVIAR</b></button>
      </div>
    </form>
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

@endpush
