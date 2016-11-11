<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Cadastrar Produtos</title>
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

  <script type="text/javascript">
    Number.prototype.formatMoney = function(c, d, t){
    var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
       return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
  </script>
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
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5>Cadastro de Produtos</h5>
          </div>
          <div class="ibox-content">
            <form class="" action="{{ url::route('produtos.create') }}" method="post">
              {{ csrf_field() }}
              <div class="row">
                <div class="form-group col-lg-5">
                  <label for="">Descrição do Produto</label>
                  <input type="text" name="descricao" class="form-control" required>
                </div>
                <div class="form-group col-lg-3">
                  <label for="">Classificação do Produto</label>
                  <select class="form-control" name="classificacao" required>
                    <option value="">Selecione uma opção</option>
                    <option value="AV">Produto Acqio - À Vista</option>
                    <option value="AP">Produto Acqio - Parcelado</option>
                    <option value="FV">Produto Franchising - À Vista</option>
                    <!-- <option value="FP">Produto Franchising - Parcelado</option> -->
                  </select>
                </div>
                <div class="form-group col-lg-2">
                  <label for="">Valor R$</label>
                  <input type="text" class="form-control" name="valor" required>
                </div><div class="form-group col-lg-2">
                  <label for="">Desconto R$</label>
                  <input type="text" class="form-control" name="desconto" value="">
                </div>
              </div>
              <div class="form-group">
                <button type="submit" name="button" class="btn btn-primary">Cadastrar</button>
              </div>
            </form>
          </div>
        </div>
        <!-- Start DataTable -->
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5>Produtos Cadastrados ({{$produtos->count()}})</h5>
          </div>
          <div class="ibox-content">
            <div class="row">
              <!-- Start table -->
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-highlight" id="tableProdutos"
                data-provide="datatable"
                  data-display-rows="25"
                  data-info="false"
                  data-search="false"
                  data-length-change="false"
                  data-paginate="true">
                  <thead>
                    <td>Data / Hora</td>
                    <td>Descrição do Produto</td>
                    <td>Classificação</td>
                    <td>Valor R$</td>
                    <td>Desconto R$</td>
                    <td>Taxa de Instalação R$</td>
                    <td>Taxa de Venda R$</td>
                    <td>TAGS <img src="http://publicdomainvectors.org/photos/primary-gnome-question.png" class="img-responsive" title="As tags representam uma forma de demarcar a relação entre os modelos dos relatórios de comissão" data-tooltip="tooltip" data-placement="bottom" style="height: 20px;" /></td>
                    <td>Ações</td>
                  </thead>
                  <tbody>
                    @foreach($produtos as $p)
                      <tr>
                        <td>{{ date('d/m/Y H:i:s', strtotime($p->created_at)) }}</td>
                        <td>{{ $p->descricao }}</td>
                        <td>
                          @if($p->classificacao == 'AV')
                            Acqio - À vista
                          @elseif($p->classificacao == 'AP')
                            Acqio - Parcelado
                          @elseif($p->classificacao == 'FV')
                            Franchising - À vista
                          @elseif($p->classificacao == 'FP')
                            Franchising - Parcelado
                          @endif
                        </td>
                        <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($p->desconto, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($p->tx_install, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($p->tx_venda, 2, ',', '.') }}</td>
                        <td>{{ $p->tags }}</td>
                        <td>
                          <a href="#modalEdit" data-id="{{ $p->id }}"  data-toggle="modal" title="Editar Produto" data-tooltip="tooltip" data-placement="top" class="btn btn-default btn-circle editProduct"><i class="fa fa-pencil-square-o"></i></a>
                        </td>
                      </tr>
                    @endforeach
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

  <!-- Modal para Editar Produto -->
  <div class="modal inmodal" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content animated bounceInRight">
            <form id="formEdit">
              {{ csrf_field() }}
              <input type="hidden" name="produtoid" value="">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Editar Produto</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="form-group col-lg-6">
                    <label>Descrição</label>
                    <input type="text" class="form-control" name="editDescription" value="">
                  </div>
                  <div class="form-group col-lg-6">
                    <label for="">Classificação do Produto</label>
                    <select class="form-control" name="editClassificacao">
                      <option value="">Selecione uma opção</option>
                      <option value="AV">Produto Acqio - À Vista</option>
                      <option value="AP">Produto Acqio - Parcelado</option>
                      <option value="FV">Produto Franchising - À Vista</option>
                      <!-- <option value="FP">Produto Franchising - Parcelado</option> -->
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-lg-6">
                    <label for="">Valor R$</label>
                    <input type="text" class="form-control" name="editValor" value="">
                  </div>
                  <div class="form-group col-lg-6">
                    <label for="">Desconto R$</label>
                    <input type="text" class="form-control" name="editDesconto" value="">
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-lg-6">
                    <label for="">Taxa de Instalação</label>
                    <input type="text" class="form-control" name="editTxInstall" value="">
                  </div>
                  <div class="form-group col-lg-6">
                    <label for="">Taxa de Venda</label>
                    <input type="text" class="form-control" name="editTxVenda" value="">
                  </div>
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

  <!-- End Modal -->


  <!-- Mainly scripts -->
  <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
  <!-- <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script> -->
  <script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

  <!-- Custom and plugin javascript -->
  <script src="{{ asset('admin/js/inspinia.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>

  <!-- Sweet alert -->
  <script src="{{ asset('admin/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


  <!-- DataTable -->
  <script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}" charset="utf-8"></script>

  <script>
  $(document).ready(function(){
    $('[data-tooltip="tooltip"]').tooltip();

    $('.editProduct').click(function(){
      var idproduto = $(this).attr('data-id');
      var tr = $(this).parent('td').parent('tr');
      console.log(tr.find('td:eq(1)').html());
      $('input[name="produtoid"]').val(idproduto);
      $.ajax({
        url: '/api/ajax/produto-details/'+idproduto,
        dataType: 'json',
        type: 'GET',
        success: function(data){
          $('input[name="editDescription"]').val(data.descricao);
          $('input[name="editValor"]').val(data.valor);
          $('input[name="editDesconto"]').val(data.desconto);
          $('input[name="editTxInstall"]').val(data.tx_install);
          $('input[name="editTxVenda"]').val(data.tx_venda);
          if(data.classificacao == "AV"){
            $('select[name="editClassificacao"]').val('AV');
          }
          else if(data.classificacao == "AP"){
            $('select[name="editClassificacao"]').val('AP');
          }
          else if(data.classificacao == "FV"){
            $('select[name="editClassificacao"]').val('FV');
          }
        }
      });
      //Submit do Form
      $('#formEdit').submit(function(e){
        e.preventDefault();
        var dados = $(this).serialize();
        var idproduto = $('input[name="produtoid"]').val();
        $.ajax({
          type: 'post',
          url: '/api/ajax/editProduct/'+idproduto,
          data: dados,
          dataType: 'json',
          success: function(data){
            if(data != 0 ){
                $("#modalEdit").modal('hide');
                swal("Feito!", "Produto editado com sucesso!", "success");
                tr.find('td:eq(1)').html($('input[name="editDescription"]').val());
                tr.find('td:eq(3)').html("R$ "+parseFloat($('input[name="editValor"]').val()).formatMoney(2, ',', '.'));
                tr.find('td:eq(4)').html("R$ "+parseFloat($('input[name="editDesconto"]').val()).formatMoney(2, ',', '.'));
                tr.find('td:eq(5)').html("R$ "+parseFloat($('input[name="editTxInstall"]').val()).formatMoney(2, ',', '.'));
                tr.find('td:eq(6)').html("R$ "+parseFloat($('input[name="editTxVenda"]').val()).formatMoney(2, ',', '.'));
            }else if( data == 0){
              swal("Erro!", "Produto não foi localizado", "error");
            }
          },
          error: function(){
            swal("Erro!", "Houve um erro na requisição, tente novamente.", "error");
          }
        });//Finish Ajax
      });//Finish Submit
    });

  });
  $(document).ready(function() {
    $('#tableProdutos').dataTable({
      dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'Produtos'},
                    {extend: 'pdf', title: 'Produtos'},

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
  });
</script>
</body>
</html>
