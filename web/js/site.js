$(document).ready(function() {
  $("#w0 button[name='signup-button']" ).prop("disabled",true);;
  $("input[name='I_accept']" ).on( "change", function() {
    if($("input[name='I_accept']").prop("checked")) {
      $("#w0 button[name='signup-button']").prop("disabled",false);
      //$("input[name='I_accept']").prop('checked', true);
    }
    else{
      $("#w0 button[name='signup-button']").prop("disabled",true);
      //$("input[name='I_accept']").prop('checked', false);
    }
  });

  //$("input[name='need_return_address']").attr('checked', false);
  if ($(".need_return_address").attr("checked") != 'checked')  $(".no_return_address").hide();
    $(".btn.add_new_address").removeClass("pull-left");
  // скраваем.отображаем return поля по чекбоксу need_return_address

  $(".need_return_address" ).on( "click", function() {   // скраваем/отображаем return поля по чекбоксу need_return_address
    if($(".need_return_address").attr("checked") != 'checked') {
      $(".no_return_address").show(500);
      $(".btn.add_new_address").addClass("pull-right");
      $(".need_return_address").attr('checked', true);
    }
    else{
      $(".no_return_address").hide(500);
      $(".btn.add_new_address").removeClass("pull-right");
      $(".need_return_address").attr('checked', false);
    }
  });
  $('.add_new_address').submit(function(){  // Дублирование данных из Send  в Return при невыбранном need_return_address
    if ($(".need_return_address").attr("checked") != 'checked') {
      $('.return_state').val($('.send_state').val());
      $('.return_zip').val($('.send_zip').val());
      $('.return_phone').val($('.send_phone').val());
      $('.return_first_name').val($('.send_first_name').val());
      $('.return_last_name').val($('.send_last_name').val());
      $('.return_company_name').val($('.send_company_name').val());
      $('.return_adress_1').val($('.send_adress_1').val())
      $('.return_adress_2').val($('.send_adress_2').val());
      $('.return_city').val($('.send_city').val());
    }
    return true;

  });

  $('body').on('keydown','.modal-body input',function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

function onlineTrace() {
  $.get('/online');
  setTimeout(onlineTrace, 60000)
}
$(function(){
    var form = $(".user-form");

    form.css({
        opacity: 1,
        "-webkit-transform": "scale(1)",
        "transform": "scale(1)",
        "-webkit-transition": ".5s",
        "transition": ".5s"
    });
});

function table_change_input(el){
  $el=$(el)
  $el
    .addClass('saving')
    .prop('disabled',true)
  post={
    'width':$el.attr('width'),
    'count':$el.attr('count'),
    'value':$el.val(),
  };
  var f_ok=table_change_ok.bind($el);
  var f_fail=table_change_fail.bind($el);
  $.post($el.closest('[href]').attr('href'),post,f_ok,'json').fail(f_fail);
}
function table_change_ok(data){
  $el=this;
  $el
    .removeClass('saving')
    .prop('disabled',false);
  $el.val(data.price);
  gritterAdd('Saving', 'Saving successful', 'gritter-success');
}

function table_change_fail(){
  $el=this;
  $el
    .removeClass('saving')
    .addClass('error')
    .prop('disabled',false);
  gritterAdd('Saving', 'Saving error', 'gritter-danger');
}