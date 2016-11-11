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
});
// Function is checked people type
function check(value){
  if(value == 1){
    var radios = document.getElementsByName('radio');
    for(var i=0; i< radios.length;i++){
      if(radios[i].checked){
        if(radios[i].value == "PF"){
          //alert('pessoa fisica');
          $('#tipo').mask('999.999.999-99');
          $('#lbl_um').html('CPF');
          $("input#tipo").attr("placeholder", "CPF");
          $('#lbl_dois').html('NOME DO CLIENTE');
          $("input#cliente").attr("placeholder", "NOME DO CLIENTE");
        }else{
          //alert('pessoa juridica');
          $('#tipo').mask('99.999.999/9999-99');
          $('#lbl_um').html('CNPJ');
          $("input#tipo").attr("placeholder", "CNPJ");
          $('#lbl_dois').html('RAZÃO SOCIAL');
          $("input#cliente").attr("placeholder", "RAZÃO SOCIAL");

          //Utilizar informações do CNPJ
          $('#tipo').blur(function(){
            var cnpj = $(this).val();
            if( cnpj.length <= 0 ) return;
            $.get("http://receitaws.com.br/v1/cnpj/", { code: cnpj },
            function(result){
              if( result.status!= 'ERROR' ){
                alert(result.message || "CNPJ não encontrado.");
                return;
              }
              $("input#cep").val( result.cep );
              $("input#cidade").val( result.municipio );
              $("input#bairro").val( result.bairro );
              $("input#rua").val( result.logradouro );
              $("input#estado").val( result.uf );
              $("input#numero").val( result.numero );
            });
          });
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
$(document).ready(function(){
  //Submit Button formBoleto
  $('#formBoleto').submit(function(){
    $('#formBoleto #resp').css('display','block');
    $('#formBoleto #resp').html("<p><b>Aguarde enquanto processamos os dados </b><i class='fa fa-spinner fa-pulse fa-3x fa-fw' style='font-size: 15px;'></i></></p>");
  });

  //Validation Next Steps
  $("#formBoleto ul li a[href='#next']").click(function(){
    var array = $('#formBoleto').serialize();


  });
});
