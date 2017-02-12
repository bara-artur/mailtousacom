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
});

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