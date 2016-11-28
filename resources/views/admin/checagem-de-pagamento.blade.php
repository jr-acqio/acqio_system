<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Admin - Checagem</title>
  <link href="https://admin.acqio.com.br/favicon.png" rel="shortcut icon" type="image/x-icon"/>
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

  <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

  <!-- DataTables -->
  <link href="{{ asset('admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('admin/css/dataTable.css') }}" media="screen" title="no title" charset="utf-8">

  <!-- DatePicker -->
  <link rel="stylesheet" href="{{ asset('admin/css/plugins/colorpicker/bootstrap-colorpicker.min.css') }} " media="screen" title="no title">
  <!-- DatePicker Style -->
  <link rel="stylesheet" href="{{ asset('admin/css/plugins/datapicker/datepicker3.css') }}" media="screen" title="no title">

  <!-- Chosen Style -->
  <link href="{{ asset('admin/css/plugins/chosen/chosen.css') }}" rel="stylesheet">

  <script type="text/javascript">
  Number.prototype.formatMoney = function(c, d, t){
    var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
  };
  </script>
  <script type="text/javascript">
  function toDate(date) {
    var ano = date[6]+date[7]+date[8]+date[9];
    var mes = date[3]+date[4];
    var dia = date[0]+date[1];
    return ano+'-'+mes+'-'+dia;
  }
  function verificaCodigo(obj){
    //O objeto pai
    $(obj).parent('div').find('i').removeClass('fa-plus').addClass('fa-spinner fa-pulse');
    var fieldDad = $(obj).parent('div').parent('div').parent('div');//closest('input[name="date[]"');
    var dataField = fieldDad.find('input[name="date[]"]');
    var dataFieldValue = dataField.val();
    console.log(obj.value);
    console.log(toDate(dataFieldValue));
    $.ajax({
      type: "GET",
      url: '/api/ajax/codigo/'+obj.value+'/data/'+toDate(dataFieldValue),
      dataType: 'json',
      success: function(msg){
        $(obj).parent('div').find('i').removeClass('fa-spinner').removeClass('fa-pulse').addClass('fa-plus');
        msg = JSON.parse(msg);
        var cliente;
        if(msg.nome != null){
          cliente = msg.nome;
        }else{
          cliente = msg.razao;
        }
        console.log(msg);
        if(msg == 0){
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-success');
          $(obj).parent('div').addClass('has-error');
          $("#messages").removeClass('alert-success');
          $("#messages").addClass('alert-danger');
          $('#messages').html("<b>Código não encontrado.</b>");
          $("#messages").show(1000);
        }
        else if(msg != 0 && msg != 1 && msg.status != "Concluida"){
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-success');
          $(obj).parent('div').addClass('has-error');
          $("#messages").removeClass('alert-success');
          $("#messages").addClass('alert-danger');
          $('#messages').html("<b>Código com status '"+msg.status+"'</b>");
          $("#messages").show(1000);
        }
        else if(msg != 0 && msg.status == "Concluida" && msg.pedido_id > 0){
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-success');
          $(obj).parent('div').addClass('has-error');
          $("#messages").removeClass('alert-success');
          $("#messages").addClass('alert-danger');
          $('#messages').html("<b>Código utilizado no <a target='_blank' href='/admin/checagem/view/"+msg.pedido_id+"'>pedido</a>: "+msg.pedido_id+" Cod Acqio: "+msg.id_acqio+"</b>");
          $("#messages").show(1000);
        }else if(msg != 0 && msg.status == "Concluida" && msg.valor_total < 0 && msg.pedido_id == 0){
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-success');
          $(obj).parent('div').addClass('has-error');
          $("#messages").removeClass('alert-success');
          $("#messages").addClass('alert-danger');
          $('#messages').html("<b>Transação foi cancelada no dia "+msg.data+" "+msg.hora+". Valor estornado: "+msg.valor_total+"");
          $("#messages").show(1000);
        }else{
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-error');
          $(obj).parent('div').addClass('has-success');
          $("#messages").removeClass('alert-danger');
          $("#messages").addClass('alert-success');
          $('#messages').html("<b>Código disponível para uso.<br>Valor do código: R$ "+(parseFloat(msg.valor_total)).formatMoney(2, ',', '.')+"</b>");
          $("#messages").show(1000);
        }
      },
      error: function(){
        swal("Erro", "Houve um erro na requisição, tente novamente.", "error");
      }
    });
  }
  function getCheckBoleto(obj){
    console.log(obj.value);
    // alert((obj.value.length));
    if(obj.value.length < 11){
      $("#messages").hide();
      $("#messages").addClass('alert-danger');
      $('#messages').html("<b>Número de Boleto inválido para pesquisa.<br>Boleto Bradesco é necessário no minimino 11 dígitos para identificação.<br>Padrão: 00000000000-0</b>");
      $("#messages").show(1000);
      return false;
    }
    else if(obj.value.length > 11 && obj.value.length < 17){
      $("#messages").hide();
      $("#messages").addClass('alert-danger');
      $('#messages').html("<b>Número de Boleto inválido para pesquisa.<br>Boleto Banco do Brasil é necessário no minimino 17 dígitos para identificação.<br>Padrão: 27316160000000000</b>");
      $("#messages").show(1000);
      return false;
    }
    $.ajax({
      type: "GET",
      url: '/api/ajax/boleto/'+obj.value,
      dataType: 'json',
      success: function(msg){//Disponivel 1, Não disponivel -1, Não encontrada 0
        msg = JSON.parse(msg);
        console.log(msg);
        if(msg == 0){
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-success');
          $(obj).parent('div').addClass('has-error');
          $("#messages").removeClass('alert-success');
          $("#messages").addClass('alert-danger');
          $('#messages').html("<b>Número de Boleto não encontrado.</b>");
          $("#messages").show(1000);
        }
        else if(msg != 0 && msg.pedido_id > 0){
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-success');
          $(obj).parent('div').addClass('has-error');
          $("#messages").removeClass('alert-success');
          $("#messages").addClass('alert-danger');
          $('#messages').html("<b>Boleto utilizado no <a target='_blank' href='/admin/checagem/view/"+msg.pedido_id+"'>pedido</a>: "+msg.pedido_id+" Cod Acqio: "+msg.id_acqio+"</b>");
          $("#messages").show(1000);
        }else{
          $("#messages").hide();
          $(obj).parent('div').removeClass('has-error');
          $(obj).parent('div').addClass('has-success');
          $("#messages").removeClass('alert-danger');
          $("#messages").addClass('alert-success');
          $('#messages').html("<b>Número de Boleto disponível para uso.<br>Valor do boleto R$ "+(parseFloat(msg.valor)).formatMoney(2, ',', '.')+"</b>");
          $("#messages").show(1000);
        }
      }
    });
  }
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
          <b>{{ session('msg') }} <a href="/admin/checagem/view/{{session('pedidoid')}}" target="_blank">Pedido {{ session('pedidoid') }}</a></b>
        </div>
        @endif
        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h5>CADASTRO DE VENDAS <i class="fa fa-credit-card" style="font-size: 18px;"></i></h5>
          </div>
          <div class="ibox-content">
            <form class="" action="{{ url::route('checagem.create') }}" method="post">
              {{ csrf_field() }}
              <div class="row">
                <div class="form-group col-lg-3">
                  <label for="">TIPO CLIENTE</label>
                  <div class="">
                    <input type="radio" required name="t_pessoa" value="PF">Pessoa Fisica
                    <input type="radio" required name="t_pessoa" value="PJ">Pessoa Jurídica
                  </div>
                </div>
                <div class="form-group col-lg-3">
                  <label for="">VENDA DE:</label>
                  <select class="form-control"  name="tipo_venda" required>
                    <option value="">SELECIONE</option>
                    <option value="CL">CLIENTE</option>
                    <option value="FR">FRANQUIA</option>
                    <option value="FD">FDA</option>
                  </select>
                </div>
                <div class="form-group col-lg-2">
                  <label for="">TIPO DE PAGAMENTO</label>
                  <div class="">
                    <input type="checkbox" class="iCheck-helper" value="true" name="cartao">Cartão</input>
                    <input type="checkbox" value="true" name="boleto">Boleto</input>
                  </div>
                </div>
                <div class="form-group col-lg-2">
                  <label for="">ID ACQIO FLOW</label>
                  <input type="text" required  class="form-control" name="id_acqio" value="">
                </div>
                <div class="form-group col-lg-2">
                  <label>VERSÃO SISTEMA</label>
                  <select class="form-control" name="versao_sis" required>
                    <option value="">SELECIONE</option>
                    <option value="VA">Versão antiga</option>
                    <option value="VN" selected="">Versão Nova</option>
                    <option value="KF">KissFlow</option>
                  </select>
                </div>
              </div>
              <!-- Informações do Cliente -->
              <div class="row">
                <div class="form-group col-lg-6">
                  <label for="cliente">CLIENTE (NOME / RAZÂO SOCIAL)</label>
                  <input type="text" required  class="form-control" name="cliente" value="">
                </div>
                <div class="form-group col-lg-6">
                  <label for="documento">DOCUMENTO (CPF / CNPJ)</label>
                  <input type="text" required class="form-control" name="documento" value="">
                </div>
              </div>
              <!-- /Informações do cliente -->
              <!-- Informações Fda e Franqueado -->
              <div class="row">
                <div class="form-group col-lg-6">
                  <label for="">FDA:</label>
                  <select class="form-control chosen-select" name="fda" required="">
                    <option value="">SELECIONE UM FDA</option>
                    @foreach($fdas as $f)
                    <option value="{{$f->id}}">{{strtoupper($f->fdaid)}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-lg-6">
                  <label for="">FRANQUEADO:</label>
                  <select class="form-control chosen-select" name="franqueado" required="">
                    <option value="">SELECIONE UM FRANQUEADO</option>
                    {{--@foreach($franqueados as $fr)
                    <option value="{{$fr->id}}">{{strtoupper($fr->franqueadoid)}}</option>
                    @endforeach  --}}
                  </select>
                </div>
              </div>
              <!-- /Informações Fda e Franqueado -->
              <!-- Produtos -->
              <div class="row">
                <div class="col-lg-12">
                  <label>PRODUTOS</label>
                  <div class="row" id="produtos">
                    <div class="form-group col-lg-6">
                      <div class="input-group">
                        <select class="form-control produtos" name="produtos[]" required>
                          <option value="">SELECIONE UM PRODUTO</option>
                          @foreach($produtos as $p)
                          <option valor="{{$p->valor}}" style="@if($p->desconto >0) background: yellow; @endif" value="{{$p->id}}">{{$p->descricao}} -
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
                        <span class="btn btn-primary input-group-addon addProduto" id="basic-addon1"><i class="fa fa-plus"></i></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Produtos -->

              <!-- Row Messages Codigos Start  -->
              <div class="alert" id="messages" style="display: none;">
              </div>
              <!-- Row Messages End -->

              <!-- Pagamentos -->
              <div class="row">
                <div class="col-lg-6" id="pag_cod" style="display: none;">
                  <div class="">
                    <label for="">CÓDIGO DE AUTORIZAÇÃO</label>
                    <div class="row" id="pagamentos">
                      <div class="form-group col-lg-6">
                        <div class="input-group">
                          <input type="text" onblur="verificaCodigo(this)" class="form-control" name="cod_auto[]" value="" maxlength="6" placeholder="Código de Autorização">
                          <span class="btn btn-primary input-group-addon addCodigo" id="basic-addon1"><i class="fa fa-plus"></i></span>
                        </div>
                      </div>
                      <div class="form-group col-lg-6 data_transacao">
                        <div class="input-group date">
                          <span class="input-group-addon btn btn-primary">
                            <i class="fa fa-calendar"></i>
                          </span>
                          <input type="text" name="date[]" class="form-control" value="" placeholder="Data Transação">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Boletos  -->
                <div class="col-lg-6" id="pag_bol" style="display: none;">
                  <div class="">
                    <label for="">NÚMERO DE BOLETO</label>
                    <div class="" id="boletos">
                      <div class="form-group" >
                        <div class="input-group">
                          <input type="text" class="form-control" onblur="getCheckBoleto(this);" name="cod_boleto[]" value="" min="17" maxlength="19">
                          <span class="btn btn-primary input-group-addon addBoleto"><i class="fa fa-plus"></i></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="">OBSERVAÇÕES</label>
                <textarea name="observacao" class="form-control" rows="5" cols="20"></textarea>
              </div>
              <div class="form-group">
                <button type="submit" style="font-weight: bold;" class="btn btn-primary">ENVIAR</button>
              </div>
            </form>
          </div>
        </div>
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

  <!-- Masked Input -->
  <script src="{{ asset('admin/js/jquery.maskedinput.js') }}" charset="utf-8"></script>

  <!-- DatePicker -->
  <script src="{{ asset('admin/js/plugins/datapicker/bootstrap-datepicker.js') }}" charset="utf-8"></script>
  <!-- DatePicker BR -->
  <script src="https://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR.min.js" charset="utf-8"></script>

  <!-- Chosen -->
  <script src="{{ asset('admin/js/plugins/chosen/chosen.jquery.js') }}"></script>
  <script src="{{ asset('admin/js/main.js') }}" charset="utf-8"></script>
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
  configChosen();
  $(document).ready(function(){
    $('input[name=t_pessoa]').on('click',function(){
      if($(this).val() == 'PF'){
        $('input[name=documento]').mask("999.999.999-99");
        $('label[for=documento]').html('CPF');
        $('label[for=cliente]').html('NOME');
      }else{
        $('input[name=documento]').mask("99.999.999/9999-99");
        $('label[for=documento]').html('CNPJ');
        $('label[for=cliente]').html('RAZÃO SOCIAL');
      }
    })
  });

  $('.data_transacao .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    format: "dd/mm/yyyy",
    language: "pt-BR"
  });

  </script>

  <script>//Script Adicionar Boletos e Códigos de Autorização
  $(document).ready(function() {

    var templateBol = '<div class="form-group" >'+
    '<div class="input-group">'+
    '<input type="text" onblur="getCheckBoleto(this);" class="form-control" name="cod_boleto[]" value="" min="17" maxlength="19">'+
    '<span class="btn btn-danger input-group-addon removeBoleto" id="basic-addon1"><i class="fa fa-trash"></i></span>'+
    '</div>'+
    '</div>';
    $("input[name='cartao']").click(function(){
      if(this.checked){
        $("#pag_cod").css('display','block');
      }else{
        $("#pag_cod").css('display','none');
      }
    });
    $("input[name='boleto']").click(function(){
      if(this.checked){
        $("#pag_bol").css('display','block');
      }else{
        $("#pag_bol").css('display','none');
      }
    });

    // Add novo boleto
    $('.addBoleto').click(function(e) {
      e.preventDefault();     //prevenir novos clicks
      $('#boletos').append(templateBol);
    });
    // Remover o div anterior
    $('#boletos').on("click",".removeBoleto",function(e) {
      e.preventDefault();
      $(this).parent('div').parent('div').remove();
    });
  });
  </script>
  <script type="text/javascript">//Script verificar código usado
  $(document).ready(function(){
    var templateCod = '<div class="row"><div class="col-lg-12"><div class="form-group col-lg-6">'+
    '<div class="input-group">'+
    '<input type="text" onblur="verificaCodigo(this)" class="form-control" name="cod_auto[]" value="" minlength="6" maxlength="6">'+
    '<span class="btn btn-danger input-group-addon removeCodigo" id="basic-addon1"><i class="fa fa-trash"></i></span>'+
    '</div>'+
    '</div>'+
    '<div class="form-group col-lg-6 data_transacao">'+
    '<div class="input-group date">'+
    '<span class="input-group-addon btn btn-primary">'+
    '<i class="fa fa-calendar"></i>'+
    '</span>'+
    '<input type="text" name="date[]" class="form-control" value="" placeholder="Data Transação">'+
    '</div>'+
    '</div></div></div>';
    // Add novo Código
    $('.addCodigo').click(function(e) {
      e.preventDefault();     //prevenir novos clicks
      $('#pagamentos').append(templateCod);
      var codigos = $("input[name='cod_auto[]']");
      console.log(codigos);
      $('.data_transacao .input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        format: "dd/mm/yyyy",
        language: "pt-BR"
      });
    });
    // Remover o div anterior
    $('#pagamentos').on("click",".removeCodigo",function(e) {
      e.preventDefault();
      $(this).parent('div').parent('div').parent('div').parent('div').remove();
    });
  });
  </script>
  <script type="text/javascript">//Script adicionar PRODUTO
  $(document).ready(function(){
    var templateProd = '<div class="form-group col-lg-6">'+
    '<div class="input-group">'+
    '<select class="form-control" name="produtos[]">'+
    '<option value="">SELECIONE UM PRODUTO</option>'+
    '@foreach($produtos as $p)'+
    '<option valor="{{$p->valor}}" style="@if($p->desconto >0) background: yellow; @endif" value="{{$p->id}}">{{$p->descricao}} -'+
    '@if($p->classificacao == "AV")'+
    ' Acqio - Á vista'+
    '@if($p->desconto > 0)'+
    ' PROMOÇÃO'+
    '@endif'+
    '@elseif($p->classificacao == "AP")'+
    ' Acqio - Parcelado'+
    '@if($p->desconto > 0)'+
    ' PROMOÇÃO'+
    '@endif'+
    '@elseif($p->classificacao == "FV")'+
    ' Franchising - Á vista'+
    '@if($p->desconto > 0)'+
    ' PROMOÇÃO'+
    '@endif'+
    '@endif - {{number_format($p->valor-$p->desconto, 2)}}'+
    '</option>'+
    '@endforeach'+
    '</select>'+
    '<span class="btn btn-danger input-group-addon removeProduto" id="basic-addon1"><i class="fa fa-trash"></i></span>'+
    '</div>'+
    '</div>';

    $('.addProduto').click(function(e) {
      e.preventDefault();     //prevenir novos clicks
      $('#produtos').append(templateProd);
      var produtos = $("select[name = 'produtos[]']");
    });
    // Remover o div anterior
    $('#produtos').on("click",".removeProduto",function(e) {
      e.preventDefault();
      $(this).parent('div').parent('div').remove();
    });
    var produtos = $("select[name = 'produtos[]']");
    $(produtos).each(function() {
      $(this).on("change", function(k, v) {
        console.log(k.target);
        console.log($(this).find('option:selected').attr('valor'));
      });
    });


  });
  </script>
</body>
</html>
