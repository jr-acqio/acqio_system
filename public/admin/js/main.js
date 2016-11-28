$(document).ready(function(){

  // Page - Checagem de Pagamento 
  $( "select[name='fda']" ).chosen().change(function(select) {
    $.ajax({
      url: '/api/ajax/fda/'+$(this).chosen().val(),
      dataType: 'json',
      success: function(data){
        var obj = JSON.parse(data);
        $('select[name="franqueado"]').find('option').remove().end();
        $('select[name="franqueado"]').chosen().append('<option value="">SELECIONE UM FRANQUEADO</option>');
        for (var i = 0; i < obj.length; i++) {
          // alert(obj[i].franqueadoid);
          $('select[name="franqueado"]').chosen().append('<option value='+obj[i].id+'>'+obj[i].franqueadoid+'</option>');
        }
        $('select[name="franqueado"]').trigger("chosen:updated");
      }
    });
  });
});
