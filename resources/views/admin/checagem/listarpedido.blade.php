<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Consultar Pedidos</title>
  <link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

  <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

  <!-- Sweet Alert -->
  <link href="{{ asset('admin/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
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
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5>Pesquisar Vendas</h5>
          </div>
          <div class="ibox-content">
            <form class="" action="{{ url::route('search.vendas') }}" method="get">

              <div class="row">
                <div class="form-group col-lg-8">
                  <label for="">CLIENTE</label>
                  <input type="text" class="form-control" name="cliente" value="" placeholder="INFORME O NOME OU RAZÃO DO CLIENTE">
                </div>
                <div class="form-group col-lg-4">
                  <label>PRODUTO</label>
                  <select class="form-control produtos" name="produto">
                    <option value="">SELECIONE UM PRODUTO</option>
                    @foreach($produtos as $p)
                    <option valor="{{$p->id}}" style="@if($p->desconto >0) background: yellow; @endif" value="{{$p->id}}">{{$p->descricao}} -
                      @if($p->classificacao == 'AV')
                      Acqio - Á vista
                      @if($p->desconto > 0)
                      PROMOÇÃO
                      @endif
                      @elseif($p->classificacao == 'AP')
                      Acqio - Parcelado
                      @if($p->desconto > 0)
                      PROMOÇÃO
                      @endif
                      @elseif($p->classificacao == 'FV')
                      Franchising - Á vista
                      @if($p->desconto > 0)
                      PROMOÇÃO
                      @endif
                      @endif - {{number_format($p->valor-$p->desconto, 2)}}
                    </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-lg-3">
                  <label>CÓDIGO DE AUTORIZAÇÃO</label>
                  <input type="text" class="form-control" name="codigo" value="" placeholder="">
                </div>
                <div class="form-group col-lg-5">
                  <label>NÚMERO DE BOLETO</label>
                  <input type="text" class="form-control" name="boleto" value="" placeholder="">
                </div>
                <div class="form-group col-lg-2">
                  <label>STATUS VENDA</label>
                  <select class="form-control" name="status">
                    <option value="">SELECIONE</option>
                    <option value="OK">OK</option>
                    <option value="CANCELADO">CANCELADO</option>
                  </select>
                </div>
                <div class="form-group col-lg-2">
                  <label>VERSÃO DO SISTEMA</label>
                  <select class="form-control" name="versao_sis">
                    <option value="">SELECIONE</option>
                    <option value="VA">Versão Antiga</option>
                    <option value="VN">Versão Nova</option>
                    <option value="KF">KissFlow</option>
                  </select>
                </div>
              </div>

              <div class="row">
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
                  <button type="submit" class="btn btn-primary pull-right" style="font-weight:bold;">Buscar <i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Start DataTable -->
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5>Registros Encontrados (@if(isset($pedidos)) {{$pedidos->count()}} @else 0 @endif)</h5>
          </div>
          <div class="ibox-content">
            <div class="row">
              <!-- Start table -->
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-highlight" id="tableChecagem"
                data-provide="datatable"
                data-display-rows="25"
                data-info="false"
                data-search="false"
                data-length-change="false"
                data-paginate="true">
                <thead>
                  <tr>
                    <th>Id Pedido</th>
                    <th>Data / Hora</th>
                    <th>Documento</th>
                    <th>Cliente</th>
                    <th>Total de Produtos</th>
                    <th>Valor Total da Compra</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($pedidos))
                  @foreach($pedidos as $p)
                  <tr>
                    <td>{{ $p->pedidoid }}</td>
                    <td>{{ date('d/m/Y H:i:s', strtotime($p->datavenda)) }}</td>
                    <td>
                      @if($p->cpf == null)
                      {{ $p->cnpj }}
                      @else
                      {{ $p->cpf }}
                      @endif
                    </td>
                    <td>
                      @if($p->cpf == null)
                      {{ $p->razao }}
                      @else
                      {{ $p->nome }}
                      @endif
                    </td>
                    <td>
                      <?php $quantProdutos = App\Models\PedidosItens::select(DB::raw('SUM(quantidade) as totalProdutos'))->where('pedido_id','=',$p->pedidoid)->first()->totalProdutos; ?>
                      {{ $quantProdutos }}
                    </td>
                    <td>
                      <?php $total = App\Models\PedidosItens::select(DB::raw('SUM(quantidade*preco_unit) as total'))->where('pedido_id','=',$p->pedidoid)->first()->total; ?>
                      R$ {{ number_format($total,2) }}
                    </td>
                    <td>@if($p->statuspedido == 0)<p style="color: red; font-weight: bold;">Cancelada</p>@else<p style="color: green; font-weight: bold;">OK</p>@endif</td>
                    <td>
                      <div class="tooltip-demo">
                        <a href="{{ url('/admin/checagem/view/'.$p->pedidoid) }}" target="_blank" class="btn btn-info btn-circle" title="Visualizar" data-toggle="tooltip" data-placement="top"><i class="fa fa-search"></i></a>
                        <a href="#modalCancel" data-id="{{ $p->pedidoid }}" data-toggle="modal" class="btn btn-danger btn-circle ver"><i class="fa fa-times" title="Cancelar Venda" data-toggle="tooltip" data-placement="top"></i></a>
                      </div>
                    </td>
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

<div class="modal inmodal" id="modalCancel" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
          <form id="formCancel">
            {{ csrf_field() }}
            <input type="hidden" name="pedidoid" value="">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h4 class="modal-title">Formulário de Cancelamento</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="">Estornar transação ?</label>
                <select class="form-control" name="estorno">
                  <option value="nao">Não</option>
                  <option value="sim">Sim</option>
                </select>
              </div>
              <div class="form-group"><label>Motivo do Cancelamento</label>
                <textarea name="motivo" required class="form-control" rows="5" cols="40"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-white" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary cancelarVenda">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<!-- Mainly scripts -->
<script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
<!-- <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script> -->
<script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('admin/js/inspinia.js') }}"></script>
<script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- DataTable -->
<script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}" charset="utf-8"></script>

<!-- Sweet alert -->
<script src="{{ asset('admin/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- DatePicker -->
<script src="{{ asset('admin/js/plugins/datapicker/bootstrap-datepicker.js') }}" charset="utf-8"></script>
<!-- DatePicker BR -->
<script src="https://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR.min.js" charset="utf-8"></script>


<script type="text/javascript">
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
$(document).ready(function(){
  $('a[href="#modalCancel"]').on('click', function(){
    var idpedido = $(this).parent('div').parent('td').parent('tr').find('td:first-child').text();
    var cliente = $(this).parent('div').parent('td').parent('tr').find('td:eq(3)').text();
    var status = $(this).parent('div').parent('td').parent('tr').find('td:eq(6)');
    var id = $(this).data('id');
    $("input[name='pedidoid']").val(idpedido);
    $("#modalCancel .modal-header .modal-title").html('Cancelamento da venda: #<b>'+idpedido+'</b><br>Cliente: '+cliente);
    $("#formCancel").submit( function(e){
      e.preventDefault();
      console.log(status.text());
      if(status.text() == "Cancelada"){
        swal("Erro", "Esta venda já está cancelada.", "error");
      }else{
        $.ajax({
          type: 'POST',
          url: '/admin/checagem/cancelar',
          data: $( this ).serialize(),
          dataType: 'json',
          success : function( data ) {
             $("#modalCancel").modal('hide');
             swal("Feito!", "Venda cancelada!", "success");
             status.html('<p style="color: red; font-weight: bold;">Cancelada</p>');
             $("#formCancel textarea[name='motivo']").val("");
          },
          error: function(){
            swal("Erro", "Houve um erro desconhecido.", "error");
          }
        });//Finish ajax
        // return false;
      }
    });
  });
})
</script>
<script>//Script Adicionar Boletos e Códigos de Autorização
$(document).ready(function() {
  $('#tableChecagem').dataTable({
    dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'Pedidos'},
                    {extend: 'pdf', title: 'Pedidos'},

                    {extend: 'print',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                    }
                    }
                ],
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
            // todayBtn: "linked",
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
