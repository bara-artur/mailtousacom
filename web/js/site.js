$(document).ready(function() {
  $("#w0 button[name='signup-button']" ).hide().attr("disabled",true);;
  $("input[name='I_accept']" ).on( "click", function() {
    if($("input[name='I_accept']").attr("checked") != 'checked') {
      $("#w0 button[name='signup-button']" ).show(500);
      $("#w0 button[name='signup-button']").attr("disabled",false);
      $("input[name='I_accept']").attr('checked', true);
    }
    else{
      $("#w0 button[name='signup-button']" ).hide(500);
      $("#w0 button[name='signup-button']").attr("disabled",true);
      $("input[name='I_accept']").attr('checked', false);
    }
  });

  $("input[name='need_return_address']" ).on( "click", function() {
    if($("input[name='need_return_address']").attr("checked") != 'checked') {
      $(".no_return_address").show(500);
      $("input[name='need_return_address']").attr('checked', true);
    }
    else{
      $(".no_return_address").hide(500);
      $("input[name='need_return_address']").attr('checked', false);
    }
  });
 /* $('.add_new_address').submit(function(){
    if ($("input[name='need_return_address']").attr("checked") == 'checked')&&(false))
    {
      alert('Заполните Return блоки');
      return false;
    }
    else{
      return true;
    }
  });*/
});

function onlineTrace() {
  $.get('/online');
  setTimeout(onlineTrace, 60000)
}