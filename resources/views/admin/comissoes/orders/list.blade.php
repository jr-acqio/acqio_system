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

<div class="row wrapper border-bottom white-bg page-heading" style="margin: 1px 1px;">
  <div class="col-lg-12">
    <h2>Ordens de Pagamento - Comissões</h2>
    <ol class="breadcrumb">
      <li>
        <a href="{{ url('/admin/dashboard') }}">Home</a>
      </li>
      <li>
        <a href="{{ url('/admin/orders') }}">Orders</a>
      </li>
      <li class="active">
        <strong>Ordens de Pagamento</strong>
      </li>
    </ol>
  </div>
</div>

<div id="app">
  <Orders></Orders>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="animated fadeInUp">
      <div class="ibox">
        <div class="ibox-content">
          <div class="row m-t-sm">
            <div class="col-lg-12">
              <div class="panel blank-panel">
                <div class="panel-heading">
                  <div class="panel-options">
                    <ul class="nav nav-tabs">
                      <li class="active"><a href="#tab-1" data-toggle="tab">Pagamentos Fda <span class="badge badge-primary">@if(isset($orders_fda)) $orders_fda->count() @endif</span></a></li>
                      <li class=""><a href="#tab-2" data-toggle="tab">Pagamentos Franqueado <span class="badge badge-primary">@if(isset($orders_franqueado)) $orders_franqueado->count() @endif</span></a></li>
                    </ul>
                  </div>
                </div>
                <div class="panel-body">

                  <div class="tab-content">
                    <div class="tab-pane active table-responsive" id="tab-1">
                      <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Fda</th>
                            <th>Relatório PDF</th>
                            <th>Total de Vendas</th>
                            <th>Valor à pagar</th>
                            <th>Status</th>
                            <th>Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(isset($orders_fda))
                          @foreach($orders_fda as $order)
                          <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ strtoupper($order->fda->fdaid) }}</td>
                            <td>
                              <a target="_blank" href="{{ url('/admin/orders/'.$order->id.'/'.basename($order->relatorio_pdf)) }}">{{basename(strtoupper($order->relatorio_pdf))}}</a>
                              <!-- <i class="fa fa-file-pdf-o"></i> -->
                            </td>
                            <td>{{ $order->comissoes()->count() }}</td>
                            <td>R$ {{ number_format($order->valor,2,',','.') }}</td>
                            <td>@if($order->status == 0) Processando @else Finalizado @endif</td>
                            <td>
                              <a href="#" class="btn btn-success btn-xs" title="Pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
                              <a href="#" class="btn btn-danger btn-xs" title="Não pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
                            </td>
                          </tr>
                          @endforeach
                        @endif
                        </tbody>
                      </table>

                    </div>
                    <div class="tab-pane table-responsive" id="tab-2">
                      <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Identificador Franqueado</th>
                            <th>Relatório PDF</th>
                            <th>Total Vendas</th>
                            <!-- <th>Total Comissão</th> -->
                            <th>Royalties</th>
                            <th>Liq. à pagar</th>
                            <th>Status</th>
                            <th>Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                        @if(isset($orders_franqueado))
                            @foreach($orders_franqueado as $order)
                          <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ strtoupper($order->franqueado->franqueadoid) }}</td>
                            <td>
                              <a target="_blank" href="{{ url('/admin/orders/'.$order->id.'/'.basename($order->relatorio_pdf)) }}">{{basename(strtoupper($order->relatorio_pdf))}}</a>
                              <!-- <i class="fa fa-file-pdf-o"></i> -->
                            </td>   
                            <td>{{ $order->comissoes()->count() }}</td>
                            <td style="@if($order->royalties()->sum('valor_original') > 0) color:red @endif">R$ {{ number_format($order->royalties()->sum('valor_original'),2,',','.') }}</td>
                            <td style="@if($order->valor < 0) color:red @endif">R$ {{ number_format($order->valor,2,',','.') }}</td>
                            <td>@if($order->status == 0) Processando @else Finalizado @endif
                            <td>
                              <a href="#" class="btn btn-success btn-xs" title="Pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
                              <a href="#" class="btn btn-danger btn-xs" title="Não pago" data-toggle="tooltip" data-placement="top"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
                            </td>
                          </tr>
                          @endforeach
                        @endif
                          
                        </tbody>
                        <tfoot>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tfoot>
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


  <!-- </div> -->



  @endsection


  @push('scripts')
  <!-- Mainly scripts -->
  <!-- <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script> -->
  <!-- <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script> -->
  <script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

  <!-- Custom and plugin javascript -->
  <script src="{{ asset('admin/js/inspinia.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

  <!-- jQuery UI -->
  <script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


  <script type="text/javascript">
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
  @endpush
