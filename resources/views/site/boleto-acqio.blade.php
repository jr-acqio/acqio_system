<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Boleto Acqio</title>
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">

  </head>
  <body style="background: #ccc;">

        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Boleto Acqio</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#">Config option 1</a>
                            </li>
                            <li><a href="#">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form id="form" action="#" class="wizard-big">
                      {{ csrf_field() }}
                        <h1>Dados do Cliente</h1>
                        <fieldset>

                          <div class="form-group">
                            <label for="" class="control-label" style=" @if ($errors->has('radio')) color:Brown; @endif ">O Cliente é</label><br>
                            <input type="radio" required name="radio" id="pf" onclick="check(1);"  value="PF" @if(old('radio')=='PF') checked='true' @endif>PESSOA FÍSICA
                            <input type="radio" required name="radio" id="pj" onclick="check(1);"  value="PJ" @if(old('radio')=='PJ') checked='true' @endif>PESSOA JURÍDICA
                          </div>

                          <div id="dados_cliente" style="display: none;">
                            <div class="form-group @if ($errors->has('tipo')) has-error @endif">
                              <label for="" class="control-label" id="lbl_um">CPF / CNPJ</label>
                              <input type="text" name="tipo" id="tipo" required class="form-control" value="{{ old('tipo') }}"  placeholder="CPF / CNPJ">
                            </div>

                            <div class="form-group @if ($errors->has('cliente')) has-error @endif">
                              <label id="lbl_dois" class=" control-label" for="">RAZÃO SOCIAL / NOME DO CLIENTE</label>
                                <input type="text" name="cliente" required id="cliente" class="form-control" value="{{ old('cliente') }}"  placeholder="RAZÃO SOCIAL / NOME DO CLIENTE">
                            </div>

                          </div>
                        </fieldset>
                        <h1>Profile</h1>
                        <fieldset>
                            <h2>Profile Information</h2>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>First name *</label>
                                        <input id="name" name="name" type="text" class="form-control required">
                                    </div>
                                    <div class="form-group">
                                        <label>Last name *</label>
                                        <input id="surname" name="surname" type="text" class="form-control required">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input id="email" name="email" type="text" class="form-control required email">
                                    </div>
                                    <div class="form-group">
                                        <label>Address *</label>
                                        <input id="address" name="address" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <h1>Warning</h1>
                        <fieldset>
                            <div class="text-center" style="margin-top: 120px">
                                <h2>You did it Man :-)</h2>
                            </div>
                        </fieldset>

                        <h1>Finish</h1>
                        <fieldset>
                            <h2>Terms and Conditions</h2>
                            <input id="acceptTerms" name="acceptTerms" type="checkbox" class="required"> <label for="acceptTerms">I agree with the Terms and Conditions.</label>
                        </fieldset>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>






    <!-- Mainly scripts -->
    <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/src/jquery.maskedinput.js') }}" type="text/javascript"></script>
    <!-- Steps -->
    <script src="{{ asset('admin/js/plugins/staps/jquery.steps.min.js') }}"></script>

    <!-- Jquery Validate -->
    <script src="{{ asset('admin/js/plugins/validate/jquery.validate.min.js') }}"></script>


    <script>
        $(document).ready(function(){
            $("#wizard").steps();
            $("#form").steps({
                bodyTag: "fieldset",
                onStepChanging: function (event, currentIndex, newIndex)
                {
                    // Always allow going backward even if the current step contains invalid fields!
                    if (currentIndex > newIndex)
                    {
                        return true;
                    }

                    // Forbid suppressing "Warning" step if the user is to young
                    if (newIndex === 3 && Number($("#age").val()) < 18)
                    {
                        return false;
                    }

                    var form = $(this);

                    // Clean up if user went backward before
                    if (currentIndex < newIndex)
                    {
                        // To remove error styles
                        $(".body:eq(" + newIndex + ") label.error", form).remove();
                        $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
                    }

                    // Disable validation on fields that are disabled or hidden.
                    form.validate().settings.ignore = ":disabled,:hidden";

                    // Start validation; Prevent going forward if false
                    return form.valid();
                },
                onStepChanged: function (event, currentIndex, priorIndex)
                {
                    // Suppress (skip) "Warning" step if the user is old enough.
                    if (currentIndex === 2 && Number($("#age").val()) >= 18)
                    {
                        $(this).steps("next");
                    }

                    // Suppress (skip) "Warning" step if the user is old enough and wants to the previous step.
                    if (currentIndex === 2 && priorIndex === 3)
                    {
                        $(this).steps("previous");
                    }
                },
                onFinishing: function (event, currentIndex)
                {
                    var form = $(this);

                    // Disable validation on fields that are disabled.
                    // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
                    form.validate().settings.ignore = ":disabled";

                    // Start validation; Prevent form submission if false
                    return form.valid();
                },
                onFinished: function (event, currentIndex)
                {
                    var form = $(this);

                    // Submit form input
                    form.submit();
                }
            }).validate({
                        errorPlacement: function (error, element)
                        {
                            element.before(error);
                        },
                        rules: {
                            confirm: {
                                equalTo: "#password"
                            }
                        }
                    });
       });
    </script>

    <script type="text/javascript">
    $(document).ready( function() {
      // Adiciona os valores de endereço a partir do blur no campo de cep
      $("#cep").blur(function(){

        var cep_code = $(this).val();
        if( cep_code.length <= 0 ) return;
        $.get("http://apps.widenet.com.br/busca-cep/api/cep.json", { code: cep_code },
        function(result){
          if( result.status!=1 ){
            //alert(result.message || "Houve um erro desconhecido");
            return;
          }
          $("input#cep").val( result.code );
          $("input#cidade").val( result.city );
          $("input#bairro").val( result.district );
          $("input#rua").val( result.address );
          $("input#estado").val( result.state );
          $("input#numero").focus();
        });
      });
    });

    $(document).ready( function(){
      $('#cep').mask('99999999');
      //Se der refresh com valores não ocultar os campos do step 1
      if($("#pf").is(":checked") || $("#pj").is(":checked")){
        $('#dados_cliente').css('display','block');
      }
      //Validando os botoes Next
      $("a[href='#next']").on('click',function(){
        var steps = $(".steps ul li").length;//Quantidade de PASSOS

      });

      //Validando quantidade
      if($("input[name='vx685']").blur(function(){
        if($("input[name='vx685']").val() < 0){
          alert('Valor para quantidade inválido! ');
          $("input[name='vx685']").val('');
        }
      }));
      if($("input[name='vx690']").blur(function(){
        if($("input[name='vx690']").val() < 0){
          alert('Valor para quantidade inválido! ');
          $("input[name='vx690']").val('');
        }
      }));

    });
    // Function is checked people type
    function check(value){
      if(value == 1){
        var radios = document.getElementsByName('radio');
        for(var i=0; i< radios.length;i++){
          if(radios[i].checked){
            if(radios[i].value == "PF"){
              //alert(radios[i].value);
              //$('#tipo').val('');
              $('#dados_cliente').css('display','none');
              $('#dados_cliente').fadeIn();
              $('#tipo').mask('999.999.999-99');
              $('#lbl_um').html('CPF');
              $("input#tipo").attr("placeholder", "CPF");
              $('#lbl_dois').html('NOME DO CLIENTE');
              $("input#cliente").attr("placeholder", "NOME DO CLIENTE");
            }else{
              //alert(radios[i].value);
              //$('#tipo').val('');
              $('#dados_cliente').css('display','none');
              $('#dados_cliente').fadeIn();
              $('#tipo').mask('99.999.999/9999-99');
              $('#lbl_um').html('CNPJ');
              $("input#tipo").attr("placeholder", "CNPJ");
              $('#lbl_dois').html('RAZÃO SOCIAL');
              $("input#cliente").attr("placeholder", "RAZÃO SOCIAL");
            }
          }
        }
      }
      else if(value == 2){
        var radios = document.getElementsByName('radio2');
        for(var i=0; i< radios.length;i++){
          if(radios[i].checked){
            if(radios[i].value == "FDA"){
              $('#lbl_fda_franqueado').html('FDA');
              $("input#fda_franqueado").attr("placeholder", "NOME DO FDA");
              $('#lbl_razao_nome').html('RAZAO SOCIAL / NOME DO FDA');
              $("input#razao_nome").attr("placeholder", "RAZAO SOCIAL / NOME DO FDA");
            }else{
              $('#lbl_fda_franqueado').html('FRANQUEADO');
              $("input#fda_franqueado").attr("placeholder", "NOME DO FRANQUEADO");
              $('#lbl_razao_nome').html('RAZAO SOCIAL / NOME DO FRANQUEADO');
              $("input#razao_nome").attr("placeholder", "RAZAO SOCIAL / NOME DO FRANQUEADO");
            }
          }
        }
      }
    }

    </script>
  </body>
</html>
