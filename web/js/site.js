$(document).ready(function() {
  $('.secundar_address').hide();
  $('.show_after_all_button').hide();
  init_address_edit();
  init_js_validation();
  init_main_table_checkbox();
  init_collapse_buttons();

  init_ajax_send_lb_oz_tn();
  ajax_send_admin_status_onchange();
 // ajax_send_admin_user_status_onchange();
  init_show_include_payments();

  //в модалках запрет отправки по Enter
  $('body').on('keydown','.modal-content input',function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $("#w1 button[name='signup-button']" ).prop("disabled",true);
  $("input[name='I_accept']" ).on( "change", function() {
    if($("input[name='I_accept']").prop("checked")) {
      $("#w1 button[name='signup-button']").prop("disabled",false);
      //$("input[name='I_accept']").prop('checked', true);
    }
    else{
      $("#w1 button[name='signup-button']").prop("disabled",true);
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
    all_lb =  document.getElementsByName('lb');
    all_valid = true;
    for (i=0;i<all_lb.length;i++) {
      if (valid_order_create(all_lb[i]) == false) all_valid = false;
    }
    if (document.getElementsByClassName('has-error').length!=0) all_valid = false;

    if (!all_valid){
      gritterAdd('Error','Missing a required field. ','gritter-danger');
      return false;
    }
    return true;
  })

  $(".reset_filter").on("click", function (){
    event.preventDefault();

    elements = $(this).parents('form:first').find("input,select");  // выборка внутри формы всех селектов и инпутов
    for (var i = 0; i < elements.length; i++) {
      var input = elements[i];
      if ((input.type != 'hidden')||((' ' + input.className + ' ').indexOf(' AutoCompleteId ') > -1)) input.value = ''; // проверка наличия класса AutoCompleteId
    }

  });

  $('.hidden_block_communication').on('change',function(){
    el=$('.'+$(this).attr('name'))
    if(el.length<1)return;
    if(!this.checked){
      el.hide();
    }else {
      el.show();
      el.removeClass('has-error')
      el.find('.help-block').remove()
    }
    els=$('.hidden_block_communication:not(:checked)');
    sum=0;
    vat=0;
    for (i=0;i<els.length;i++){
      sum+=parseFloat(els.eq(i).attr('sum'));
      vat+=parseFloat(els.eq(i).attr('vat'));
    }
    $('.total_sum').text(sum.toFixed(2));
    $('.total_vat').text(vat.toFixed(2));
  })
});

function show_err(el,txt){
  if(!el.hasClass('has-error')) {
    el.addClass('has-error');
    el.append("<div class=\"help-block\">"+txt+"</div>");
  }
}
function hide_err(el){
  el.removeClass('has-error');
  el.find('.help-block').remove();
}
function valid_order_create(elemForm){
  valid=true;

  lb=$(elemForm).parents('form:first').find('[name=lb]');
  oz=$(elemForm).parents('form:first').find('[name=oz]');

  //for(i=0;i<lb.length;i++){
    el=$(lb).closest('.label_valid');
    if((!lb.val()) ||
        (!oz.val()) ||
      (parseInt(lb.val())+parseInt(oz.val())/16)==0)
    {
      valid=false;
      show_err(el,"Field scale required.");
    }else{
      if(
          (parseInt(oz.val())>=16) ||
          (parseInt(oz.val())<0)
      ){
        valid=false;
        show_err(el,"The value of Oz can not be more than 15.");
      }else {
        if(
            (parseInt(lb.val())>=101) ||
            (parseInt(lb.val()))<0
        ){
          valid=false;
          show_err(el,"The value of Lb can not be more than 100.");
        }else {
          hide_err(el);
        }
      }
    }
  //}

  els=$(elemForm).parents('form:first').find('[name=track_number]');
  els_type=$(elemForm).parents('form:first').find('[name=track_number_type]');
  //for(i=0;i<els.length;i++){
    el=$(els).closest('.label_valid');
    if (els_type.prop('checked')) {
      hide_err(el);
      els.val(0);
      els.parent().hide();
      els_type.val(1);
    }
    else {
      els.parent().show();
      els_type.val(0);
      if ((!els.val()) || (els.val().length < 4)) {
        valid = false;
        show_err(el, "Track number is required.");
      } else {
        hide_err(el);
      }
    }
 // }
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

  function check_name_input(){
    if ($('.first_name').val() == "") $('.first_name').val("-");
    if ($('.last_name').val() == "") $('.last_name').val("-");
  }

  function on_address_submit(){
    if ($(".show_company").prop('checked')==false) {
      $('.company_name').val('Personal address');
    }
    else {
      check_name_input();
    }
    return true;
  }

  $('.first_name, .last_name').on('change', function (){
    if ($(".show_company").prop('checked')==true) {
      check_name_input();
    }
  });

  $('.add_new_address').submit(on_address_submit());

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
      check_name_input();
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

function  no_letters_in_input(evt){
    if ( ( evt.keyCode >= 48 && evt.keyCode <= 57 )) return;
    else  evt.preventDefault();
}

function  only_letters_in_input(evt){
        if(
            ( evt.keyCode >= 97 && evt.keyCode <= 122 ) ||
            ( evt.keyCode >= 65 && evt.keyCode <= 90 )|| (evt.keyCode==32) )
            return ;
        else evt.preventDefault();
}

function  only_no_foreign_letters_in_input(evt){
        if(
            ( evt.keyCode >= 48 && evt.keyCode <= 57 ) ||
            ( evt.keyCode >= 97 && evt.keyCode <= 122 ) ||
            ( evt.keyCode >= 65 && evt.keyCode <= 90 )||
            (evt.keyCode==44)||    // запятая
            (evt.keyCode==46)||    // точка
            (evt.keyCode==64)||    // @
            (evt.keyCode==32) )   // пробел
            return;
        else evt.preventDefault();
}

function init_js_validation()
{
          $('body').on('keypress', '.letters', only_letters_in_input);
          $('body').on('keypress', 'input,textarea', only_no_foreign_letters_in_input);
          $('body').on('keypress', '.num', no_letters_in_input);
}

function init_ajax_send_lb_oz_tn(){
  $( ".lb-oz-tn-onChange" ).on("change",function(){ajax_send_lb_oz_tn(this)});
  $( ".lb-oz-tn-onChange" ).on("keypress", function (e){
    if (e.keyCode == 13) {
      ajax_send_lb_oz_tn(this);
    }
  });
}

function ajax_send_lb_oz_tn(elemForm) {
    index = Math.floor($('.lb-oz-tn-onChange').index(elemForm) /4); // высчитываем номер посылки, которая вызвала данное событие
    if(!valid_order_create(elemForm))return false;
    var msg   = $(elemForm).parents('form:first').serialize();

    $.ajax({
      type: 'POST',
      url: 'orderElement/create-order',
      data: msg,
      success: function(data) {
        $('.resInd'+index).html(data).css( "color", "blue");
      },
      error:  function(xhr, str){
        gritterAdd('Error','Error: '+xhr.responseCode,'gritter-danger');
      }
    });
}


function ajax_send_admin_status_onchange(){
  $( ".status_droplist" ).change(function() {
    elem = this;
    elem.classList.add('ajax_proccessing');
    elem.classList.remove("ajax_proccessing_error");
    elem.disabled = true;
    //index = Math.floor($('.lb-oz-tn-onChange').index(elemForm) /3);

    name = elem.name;
    payStatus = 'none';
    ordStatus = 'none';
    order_id = name.substr(9,name.length-9);

    if (name.substr(0,3)=='pay') payStatus = elem.value;
    if (name.substr(0,3)=='ord') ordStatus = elem.value;
    $.ajax({
      type: 'POST',
      url: 'order/update',
      data: { order_id: order_id, order_status: ordStatus ,payment_state : payStatus },
      success: function(data) {
        elem.disabled = false;
        if (data)  {
          gritterAdd('Saving', 'Saving successful', 'gritter-success');
          elem.classList.remove("ajax_proccessing");
        }
        else {
          gritterAdd('Saving', 'Saving error. {'+order_id+'} payStatus='+payStatus+' ordStatus='+ordStatus, 'gritter-danger');
          elem.classList.remove("ajax_proccessing");
          elem.classList.add('ajax_proccessing_error');
        }
      },
      error:  function(xhr, str){
        gritterAdd('Error','Error: '+xhr.responseCode,'gritter-danger');
        elem.disabled = false;
        elem.classList.remove("ajax_proccessing");
        elem.classList.add('ajax_proccessing_error');
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

function form_parcel_create_type(el){
  if($(el).prop('checked')){
    $('.form_parcel_create_type_1').show();
    $('.form_parcel_create_type_0').hide();
  }else{
    $('.form_parcel_create_type_1').hide();
    $('.form_parcel_create_type_0').show();
  }
}

function AutoCompleteUserSelect(e,ui){
  if(ui.item.id || ui.item.id>1){
    $('.admin_choose_user').prop('disabled',false);
    $('.AutoCompleteId').val(ui.item.id);
  }else{
    $('.admin_choose_user').prop('disabled',true);
  }
}

function main_table_checkbox(current_element){
 // $(current_element).parents('td:first').css("background-color","red");
  elem_checked = $(".checkBoxParcelMainTable:checked");
  if ($(current_element).prop("checked")) {
    if (elem_checked.length > 1) { // выделено больше одного элемента - поэтому надо проверять на типы статусов
      if (($(current_element).attr('user') != elem_checked[0].getAttribute('user')) ||   // проверка необходима для работы админа
        ($(current_element).attr('user') != elem_checked[1].getAttribute('user'))) {
        gritterAdd('Error', "You can't combine parcels of 2 different users", 'gritter-danger');
        $(current_element).prop("checked", false);
        elem_checked = $(".checkBoxParcelMainTable:checked"); // убираем из списка отмененное выше выделение
      }else {
        if (($(current_element).attr('name') != elem_checked[0].name) ||
          ($(current_element).attr('name') != elem_checked[1].name)) { // разные статусы у выделения. Надо отменить
          gritterAdd('Error', "You can't combine parcels with Draft and the other Status", 'gritter-danger');
          $(current_element).prop("checked", false);
          elem_checked = $(".checkBoxParcelMainTable:checked"); // убираем из списка отмененное выше выделение
        } else {// выделение корректно
        }
      }
    }
  }

  if (elem_checked.length>0){
    if (elem_checked[0].name == 'InSystem') {
      elem_type = 'Draft';
    }else{
      elem_type = 'InSystem';
    }
    user_id = elem_checked[0].getAttribute('user');
    // берем элементы с другим статусом ИЛИ другого user_id
    elems_prohibeted = $(" [name='"+elem_type+"'], .checkBoxParcelMainTable[user !='"+user_id+"']");
    console.log(elems_prohibeted.length);
    elems_prohibeted.addClass('select_prohibited').css("background-color","red");
    elems_prohibeted.prop("disabled",true);

  }else{
    elems_prohibeted = $(".select_prohibited");
    elems_prohibeted.parents('td').fadeTo(500, 1);
    elems_prohibeted.prop("disabled",false);
    elems_prohibeted.removeClass('select_prohibited');
  }



  parcel_ids ="";
  string = "empty";
  elem_checked.each(function(i,elem) {
    if (parcel_ids == "") {
      parcel_ids = '/'+this.id;
      //string = this.id;
      string=1;
    }else {
      parcel_ids = parcel_ids + "_" + this.id;
      //string = string + " " + this.id;
      string++;
    }
  });
  if (string!="empty"){
    string = string + " ( " + elem_checked[0].name+" type)";
    $('.'+elem_checked[0].name+'_show').attr('disabled',false)
    if(elem_checked[0].name=="InSystem"){
      $('.gr_update_text').text("View parcels")
    }else{
      $('.gr_update_text').text("Update parcels")
    }
  }else{
    $('.InSystem_show,.Draft_show').attr('disabled',true)
  }

  $("#for_group_actions").text("Checked parcels: " + string);
  $("#group-update").attr("href","/orderElement/group-update"+parcel_ids);
  $("#group-print").attr("href","/orderElement/group-print"+parcel_ids);
  $("#group-print-advanced").attr("href","/orderElement/group-print-advanced"+parcel_ids);
  $("#group-delete").attr("href","/orderElement/group-delete"+parcel_ids);
}

function init_main_table_checkbox(){
  main_table_checkbox();
  $(".checkBoxParcelMainTable").on('change',function(){
    main_table_checkbox(this);
  })
}

function init_collapse_buttons(){
  $("#collapse_filter").on("click", function(){
    $("#collapseTableOptions").collapse("hide");
  });
  $("#collapse_columns").on("click", function(){
    $("#collapse").collapse("hide");
  })
}
 function init_show_include_payments(){
   $(".show_include_payments").on("click", function(event){
     id = $(this).attr('payment_id');
     event.preventDefault();
     $.ajax({
       type: 'POST',
       url: 'payment/includes',
       data: {payment_id: $(this).attr('payment_id')},// payment_id'+$(this).attr('payment_id'),
       success: function(data) {
         $("[payment_id = '"+id+"']").parents('td').html(data);
       },
       error:  function(xhr, str){
         gritterAdd('Error','Error: '+xhr.responseCode,'gritter-danger');
       }
     });
   })
 }
