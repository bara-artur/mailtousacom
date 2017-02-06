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

  //$("input[name='need_return_address']").attr('checked', false);
  if ($("input[name='need_return_address']").attr("checked") != 'checked')  $(".no_return_address").hide();  // скраваем.отображаем return поля по чекбоксу need_return_address

  $("input[name='need_return_address']" ).on( "click", function() {   // скраваем/отображаем return поля по чекбоксу need_return_address
    if($("input[name='need_return_address']").attr("checked") != 'checked') {
      $(".no_return_address").show(500);
      $("input[name='need_return_address']").attr('checked', true);
    }
    else{
      $(".no_return_address").hide(500);
      $("input[name='need_return_address']").attr('checked', false);
    }
  });
  $('.add_new_address').submit(function(){  // Дублирование данных из Send  в Return при невыбранном need_return_address
    if (($("input[name='need_return_address']").attr("checked") != 'checked')&&($('.return_first_name').val()=='')) $('.return_first_name').val($('.send_first_name').val());
    if (($("input[name='need_return_address']").attr("checked") != 'checked')&&($('.return_last_name').val()=='')) $('.return_last_name').val($('.send_last_name').val());
    if (($("input[name='need_return_address']").attr("checked") != 'checked')&&($('.return_company_name').val()=='')) $('.return_company_name').val($('.send_company_name').val());
    if (($("input[name='need_return_address']").attr("checked") != 'checked')&&($('.return_adress_1').val()=='')) $('.return_adress_1').val( $('.send_adress_1').val())
    if (($("input[name='need_return_address']").attr("checked") != 'checked')&&($('.return_adress_2').val()=='')) $('.return_adress_2').val($('.send_adress_2').val());
    if (($("input[name='need_return_address']").attr("checked") != 'checked')&&($('.return_city').val()=='')) $('.return_city').val($('.send_city').val());
      return true;

  });
});

function onlineTrace() {
  $.get('/online');
  setTimeout(onlineTrace, 60000)
}