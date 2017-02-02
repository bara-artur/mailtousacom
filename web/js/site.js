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
});

function onlineTrace() {
  $.get('/online');
  setTimeout(onlineTrace, 60000)
}