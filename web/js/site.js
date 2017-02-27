$(document).ready(function() {
  $('.secundar_address').hide();
  $('.show_after_all_button').hide();
  init_address_edit();
  no_letters_in_input();
  ajax_send_lb_oz_tn_onchange();


  //в модалках запрет отправки по Enter
  $('body').on('keydown','.modal-content input',function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

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

  $('.go_to_order').on('click',function(){
    if (!valid_order_create()){
      gritterAdd('Error','Missing a required field. ','gritter-danger');
      return false;
    }
    return true;
  })
});

function show_err(el,txt){
  if(!el.hasClass('has-error')) {
    el.addClass('has-error')
    el.append("<div class=\"help-block\">"+txt+"</div>")
  }
}
function hide_err(el){
  el.removeClass('has-error')
  el.find('.help-block').remove()
}
function valid_order_create(){
  valid=true
  lb=$('[name=lb]');
  oz=$('[name=oz]');
  for(i=0;i<lb.length;i++){
    el=$(lb[i]).closest('.label_valid');
    if(!lb[i].value ||
      !oz[i].value ||
      (parseInt(lb[i].value)+parseInt(oz[i].value)/16)==0
    ){
      valid=false;
      show_err(el,"Field scale required.");
    }else{
      if(
        parseInt(oz[i].value)>16 ||
        parseInt(oz[i].value)<0
      ){
        valid=false;
        show_err(el,"The value of Oz can not be more than 15.");
      }else {
        if(
          parseInt(lb[i].value)>101 ||
          parseInt(lb[i].value)<0
        ){
          valid=false;
          show_err(el,"The value of Lb can not be more than 100.");
        }else {
          hide_err(el);
        }
      }
    }
  }

  els=$('[name=track_number]');
  for(i=0;i<els.length;i++){
    el=$(els[i]).closest('.label_valid')
    if(!els[i].value ||
      els[i].value.length<4
    ){
      valid=false;
      show_err(el,"Track number is required.");
    }else{
      hide_err(el);
    }
  }


  return valid;
}

var popup = (function() {
  var conteiner;
  var mouseOver = 0;
  var timerClearAll = null;
  var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
  var time = 3000;

  var _setUpListeners = function() {
    $('body').on('click', '.notification_close', _closePopup);
    $('body').on('mouseenter', '.notification_container', _onEnter);
    $('body').on('mouseleave', '.notification_container', _onLeave);
  };

  var _onEnter = function(event) {
    if(event)event.preventDefault();
    if (timerClearAll!=null) {
      clearTimeout(timerClearAll);
      timerClearAll = null;
    }
    conteiner.find('.notification_item').each(function(i){
      var option=$(this).data('option');
      if(option.timer) {
        clearTimeout(option.timer);
      }
    });
    mouseOver = 1;
  };

  var _onLeave = function() {
    conteiner.find('.notification_item').each(function(i){
      $this=$(this);
      var option=$this.data('option');
      if(option.time>0) {
        option.timer = setTimeout(_closePopup.bind(option.close), option.time - 1500 + 100 * i);
        $this.data('option',option)
      }
    });
    mouseOver = 0;
  };

  var _closePopup = function(event) {
    if(event)event.preventDefault();

    var $this = $(this).parent();
    $this.on(animationEnd, function() {
      $(this).remove();
    });
    $this.addClass('notification_hide')
  };

  var open = function(data) {
    var option = {time : (data.time||data.time===0)?data.time:time};
    if (!conteiner) {
      conteiner = $('<ul/>', {
        'class': 'notification_container'
      });

      $('body').append(conteiner);
      _setUpListeners();
    }

    var li = $('<li/>', {
      class: 'notification_item'
    });

    if (data.type){
      li.addClass('notification_item-' + data.type);
    }

    var close=$('<span/>',{
      class:'notification_close'
    });
    option.close=close;
    li.append(close);

    if(data.title && data.title.length>0) {
      var title = $('<p/>', {
        class: "notification_title"
      });
      title.html(data.title);
      li.append(title);
    }

    var content = $('<div/>',{
      class:"notification_content"
    });
    content.html(data.message);

    li.append(content);

    conteiner.append(li);

    if(option.time>0){
      option.timer=setTimeout(_closePopup.bind(close), option.time);
    }
    li.data('option',option)
  };

  return {
    open: open
  };
}());

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
    'weight':$el.attr('weight'),
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

function init_address_edit(){
  function on_address_submit(){
    if ($(".show_company").prop('checked')==false) {
      $('.company_name').val('Personal address');
    }
    return true;
  }

  $('.add_new_address').submit(on_address_submit);

  company_blk=$('.company_name.form-control').parent();
  if ($('.show_company').prop('checked')==false){
    $('.company_name')
      .val('Personal address')
      .data('val', '');
    company_blk.hide(500);
  }
  $('.show_company').on("click", function(){
    company_blk=$('.company_name.form-control').parent();
    if ($('.show_company').prop('checked')==false) {
      v = $('.company_name').val();
      $('.company_name')
        .val('Personal address')
        .data('val', v);
      company_blk.hide(500);
    }else{
      $('.company_name').val($('.company_name').data('val'));
      company_blk.show(500);
    }
  });
  $(".show_all_addresses").on("click", function(){
    $('.secundar_address').show(500);
    $('.show_after_all_button').show();
    $(".show_all_addresses").hide();
    $(".main_address_button").hide();
  });
}

function  no_letters_in_input(){
  $("input.num").keydown(function(event) {
    // Разрешаем нажатие клавиш backspace, Del, Tab и Esc
    if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
        // Разрешаем выделение: Ctrl+A
        (event.keyCode == 65 && event.ctrlKey === true) ||
        // Разрешаем клавиши навигации: Home, End, Left, Right
        (event.keyCode >= 35 && event.keyCode <= 39)) {
      return;
    }
    else {
      // Запрещаем всё, кроме клавиш цифр на основной клавиатуре, а также Num-клавиатуре
      if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
        event.preventDefault();
      }
    }
  });
}

function ajax_send_lb_oz_tn_onchange(){
  $( ".lb-oz-tn-onChange" ).change(function() {
    if(!valid_order_create())return false;
    var msg   = $(this).parents('form:first').serialize();
    $.ajax({
      type: 'POST',
      url: 'orderElement/create-order',
      data: msg,
      success: function(data) {
        $('#results').html(data);
      },
      error:  function(xhr, str){
        gritterAdd('Error','Error: '+xhr.responseCode,'gritter-danger');
      }
    });
  });
}

function init_order_border(){
  $('.order_agreement').submit(function(){  // действия перед submit формы
    if ($("#order-agreement").prop('checked')==false) {
      gritterAdd('Error','you must agree','gritter-danger');
      return false;
    }
    return true;
  });

  if ($("#order-agreement").prop('checked')==false) {
    $('.order_agreement').find('[type=submit],.on_agreement').attr('disabled',true)
  }

  $("#order-agreement").on("change", function(){
    $('.order_agreement').find('[type=submit],.on_agreement').attr('disabled',!this.checked)
  });

}
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})