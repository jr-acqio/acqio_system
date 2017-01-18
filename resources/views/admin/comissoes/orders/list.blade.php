@extends('layouts.master')

@section('title','Ordens de Pagamento - Comissões')
@push('links')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

<link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

<!-- DataTables -->
<link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">

@endpush

@section('content')

<!-- <div id="app"> -->
  <router-view></router-view>
<!-- </div> -->

@endsection


@push('scripts')
<!-- Mainly scripts -->

<!-- <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script> -->
<!-- <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('admin/js/inspinia.js') }}"></script>
<script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>



<script type="text/javascript">
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>
@endpush
