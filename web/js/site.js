$(document).ready(function() {
  $('.secundar_address').hide();
  $('.show_after_all_button').hide();
  init_address_edit();
  init_js_validation();
  init_main_table_checkbox();
  init_collapse_buttons();
  init_button_clearParcelsIdCookie();
  init_button_updateParcelsIdCookie();
  init_ajax_send_lb_oz_tn();
  ajax_send_admin_status_onchange();
 // ajax_send_admin_user_status_onchange();
  init_show_include_payments();

  //в модалках запрет отправки по Enter
  $('body').on('keydown','.modal-content input',function(event){
    code = event.keyCode||event.charCode; // для Chrome || Firefox
    if(code == 13) {
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
    price=0;
    qst=0;
    gst=0;
    for (i=0;i<els.length;i++){
      sum+=parseFloat(els.eq(i).attr('sum'));
      price+=parseFloat(els.eq(i).attr('price'));
      qst+=parseFloat(els.eq(i).attr('qst'));
      gst+=parseFloat(els.eq(i).attr('gst'));
    }
    $('.tot_sum').text(sum.toFixed(2));
    $('.tot_price').text(price.toFixed(2));
    $('.tot_qst').text(qst.toFixed(2));
    $('.tot_gst').text(gst.toFixed(2));

  })

  $('body').on('click','[role="modal-remote"]',function(){
    if($(this).hasClass('big_model')) {
      $('#ajaxCrudModal').addClass("modal-lg")
    }else{
      $('#ajaxCrudModal').removeClass("modal-lg")
    }
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

valid_gritter=new Array();
function show_gritter(current_element){ // проверяем показывать ли для данного элемента гриттер или нет.
  flag = 0;
  if (valid_gritter.length>0) valid_gritter.forEach(function(item, i, arr) {
    if (item==current_element) flag =1;  // Показываем только один раз для текущей страницы
  });
  if (flag==0) {   // если для этого элемента ещё не вызывали гриттер
    valid_gritter.push(current_element);
    gritterAdd('Warning','Warning: prohibited key pressed','gritter-warning');
  }

}

function  canadian_zip_key_control(evt){ // формат zip *** ***
  code = evt.keyCode||evt.charCode; // для Chrome || Firefox
  if (this.value.length==3) this.value = this.value + ' ';
  if(( code >= 48 && code <= 57 ) || ( code >= 97 && code <= 122 ) || ( code >= 65 && code <= 90 )) {
    if (this.value.length == 7) {
      evt.preventDefault();
    }
    else {
      return;
    }
  }
  else {
    show_gritter(this);
    evt.preventDefault();
  }
}

function  float_in_input(evt){
  code = evt.keyCode||evt.charCode; // для Chrome || Firefox

  if ( ( code >= 48 && code <= 57 )||(code==13)||(code==46)||(code==44)) return;
  else  {
    show_gritter(this);
    evt.preventDefault();
  }
}

function  no_letters_in_input(evt){
  code = evt.keyCode||evt.charCode; // для Chrome || Firefox
    if ( ( code >= 48 && code <= 57 )||(code==013)) return;
    else  {
      show_gritter(this);
      evt.preventDefault();
    }
}

function  only_letters_in_input(evt){
  code = evt.keyCode||evt.charCode;
        if(
            ( code >= 97 && code <= 122 ) || (code==13)||    // enter
            ( code >= 65 && code <= 90 )|| (code==32) )
            return ;
        else {
          show_gritter(this);
          evt.preventDefault();
        }
}

function  only_no_foreign_letters_in_input(evt){
  code = evt.keyCode||evt.charCode;  // для Chrome || Firefox
        if(
            ( code >= 48 && code <= 57 ) ||
            ( code >= 97 && code <= 122 ) ||
            ( code >= 65 && code <= 90 )||
            (code==44)||(code==46)||
            (code==13)||(code==32)
            ||(code==33)||(code==64)||(code==35)||(code==36)||(code==37)||(code==94)||(code==38)||(code==42)
            ||(code==40)||(code==41)||(code==95)||(code==43)||(code==45)||(code==61)||(code==8)||(code==9)||(code==39)  // !@#$%^&*()_+-=
        )
            return;
        else {
          show_gritter(this);
          evt.preventDefault();
        }
}

function init_js_validation()
{
          $('body').on('keypress', '.letters', only_letters_in_input);
          $('body').on('keypress', 'input,textarea', only_no_foreign_letters_in_input);
          $('body').on('keypress', '.num', no_letters_in_input);
          $('body').on('keypress', '.canadian_zip_key_control', canadian_zip_key_control);
          $('body').on('keypress', '.float_num', float_in_input);
}

function init_ajax_send_lb_oz_tn(){
  $( ".lb-oz-tn-onChange" ).on("change",function(){ajax_send_lb_oz_tn(this)});
  $( ".lb-oz-tn-onChange" ).on("keypress", function (e){
    code = e.keyCode||e.charCode;  // для Chrome || Firefox
    if (code == 13) {
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

  $('input[name="OrderElement[transport_data]"]').on('change',function(){
    post={
      order:odrer_id,
      value:this.value
    };
    $.post('orderInclude/border-save',post);
  })
}
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

function form_parcel_create_type(el){
  if($(el).prop('checked')){
    $('.form_parcel_create_type_1').slideDown(500);
    $('.form_parcel_create_type_0').slideUp(500);
    $('.modal-title').slideUp(500);
  }else{
    $('.form_parcel_create_type_1').slideUp(500);
    $('.form_parcel_create_type_0').slideDown(500);
    $('.modal-title').slideDown(500);
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

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function init_button_clearParcelsIdCookie(){
  $(".clearParcelsIdCookie").on('click', function (){
    setCookie('parcelCheckedId','',1);
    setCookie('parcelCheckedUser','',1);
    setCookie('doNotShowDifUserGritter','',1);
  })
}
function init_button_updateParcelsIdCookie(){
  $("#updateParcelsIdCookie").on('click', function (){
    setCookie('parcelCheckedId',$("#updateParcelsIdCookie").data('forcookie'),1);
    setCookie('parcelCheckedUser',$("#updateParcelsIdCookie").data('forusercookie'),1);

  })
}

function sendCheckedToCookie(elem_checked, oldCookie, oldCookieUser){
  var stringCoockies = '';
  var stringUsers = '';
  var difUser = 0, firstParcelUser="";
  if (oldCookieUser[0]!='') {    //  берем первую попавшуюся выделенную посылку и запоминаем юзера
    firstParcelUser = oldCookieUser[0];
  }else{
    if (elem_checked.length>0){
      firstParcelUser = elem_checked[0].getAttribute('user');
    }
  }
  if (getCookie('parcelCheckedId')!='') {
    for (i = 0; i < oldCookie.length; i++) {          //  занесли все номера посылок , которых нет на данной странице пагинации
      if (($("#" + oldCookie[i]).length == 0) && (oldCookie[i] != '')) {
        stringCoockies = stringCoockies + oldCookie[i] + ',';
        stringUsers = stringUsers + oldCookieUser[i] + ',';
        if (firstParcelUser != oldCookieUser[i]) {
          difUser = 1;
        }
      }
    }
  }
  parcelsID= getCookie('parcelCheckedId');
  parcelsUsers = getCookie('parcelCheckedUser');
  for(var i = 0; i <elem_checked.length; i++) {     // заносим все номера посылок, которые есть на данной странице пагинации
    stringCoockies = stringCoockies + elem_checked[i].getAttribute('id')+',';
    stringUsers = stringUsers + elem_checked[i].getAttribute('user')+',';
    if (firstParcelUser!=elem_checked[i].getAttribute('user')) {
      difUser = 1;
    }
  }
  stringCoockies = stringCoockies.substring(0, stringCoockies.length - 1); // удаляем запятую
  stringUsers = stringUsers.substring(0, stringUsers.length - 1); // удаляем запятую
  setCookie('parcelCheckedId',stringCoockies,1);
  setCookie('parcelCheckedUser',stringUsers,1);
  if ((getCookie('multiUserMode')=='1')&&(difUser==0)){    // выдаем гриттер при переключении многопользовательского режима
    gritterAdd('One user mode', '', 'gritter-success');
  }else {
    if ((getCookie('multiUserMode') == '0') && (difUser == 1)) {  // выдаем гриттер при переключении многопользовательского режима
      gritterAdd('MultiUser mode', '', 'gritter-warning');
    }
  }
  setCookie('multiUserMode',difUser,1);
  if (elem_checked.length>0) {
    setCookie('parcel_elem_type', elem_checked[0].getAttribute('name'), 1);
    setCookie('parcel_user_id', elem_checked[0].getAttribute('user'), 1);
  }
}

canChooseDifUser = null;
function main_table_checkbox(current_element){
  if ($('.group-admin-view').length!=0) admin = 1; else admin =0;
 // $(current_element).parents('td:first').css("background-color","red");
  current_id = null;
  if (current_element) { // не должно срабатывать по f5
    current_id = $(current_element).prop('id');
  }
  refreshParcel = null; // удаляем из куки чекбокс, который вызвал событие click
  oldCookie = getCookie('parcelCheckedId').split(',');        // все чекбоксы со всех страниц
  oldCookieUser = getCookie('parcelCheckedUser').split(',');        // все юзеры со всех страниц
  for (i = 0; i < oldCookie.length; i++) {                           // выделяем чекбоксы - для обновления по f5
    if (oldCookie[i] != current_id) {
      $("#" + oldCookie[i]).prop("checked", true);
    }else{
      refreshParcel = i;
    }
  }
  if (refreshParcel!=null){   // удаляем из массива старых куки текущий чекбокс
    oldCookie.splice(refreshParcel, 1);
    oldCookieUser.splice(refreshParcel, 1);
  }

  elem_checked = $(".checkBoxParcelMainTable:checked");// выделенные чекбоксы на этой странице
  sendCheckedToCookie(elem_checked, oldCookie, oldCookieUser);
  elem_cookie_type = getCookie('parcel_elem_type');
  user_id = getCookie('parcel_user_id');
  if (getCookie('parcelCheckedId')!=''){ // если выделена хотя бы одна посылка
    if (getCookie('multiUserMode')=='1') {
      $('.labelDifUserId').show().css('background-color',"orange");
    }else{
      $('.labelDifUserId').hide();
    }
    $('.clearParcelsIdCookie').show();
    if (elem_cookie_type == 'InSystem') {
      elem_type = 'Draft';
    }else{
      elem_type = 'InSystem';
    }
    //user_id = elem_checked[0].getAttribute('user');
    // берем элементы с другим статусом ИЛИ другого user_id
    if (admin==0) {   // если обычный юзер, то гасим посылки других пользователей и другой тип посылок
      elems_prohibeted = $(" [name='"+elem_type+"'], .checkBoxParcelMainTable[user !='"+user_id+"']");
    }else{    // если админ, то подсвечиваем других пользователей и гасим другой тип посылок
      elems_prohibeted = $(" [name='"+elem_type+"']");
      elems_difUserID = $(" .checkBoxParcelMainTable[user !='"+user_id+"']").addClass("elems_difUserID");
    }
    elems_prohibeted.addClass('select_prohibited').css("background-color","red");
    elems_prohibeted.prop("disabled",true);
  }else{    // не выделена ни одна посылка. Очищаем куки. Включаем все чекбоксы. Скрываем кнопку очистки
    $('.labelDifUserId').hide();
    setCookie('parcel_elem_type','',1);
    setCookie('parcel_user_id','',1);
    setCookie('multiUserMode',0,1);
    $('.clearParcelsIdCookie').hide();
    elems_prohibeted = $(".select_prohibited");
    elems_prohibeted.parents('td').fadeTo(500, 1);
    elems_prohibeted.prop("disabled",false);
    elems_prohibeted.removeClass('select_prohibited');
  }

  parcel_ids ="";
  stringCoockies = getCookie('parcelCheckedId');
  if (stringCoockies.length>0){
    if (stringCoockies.substr(-1)==',') stringCoockies = stringCoockies.substring(0, stringCoockies.length - 1);
    string = stringCoockies.split(',').length;
    parcel_ids = '/'+stringCoockies.replace(/,/g,'_');
  }else{
    string = "empty";
    parcel_ids = '/'+this.id;
  }
 /* elem_checked.each(function(i,elem) {
    if (parcel_ids == "") {
      parcel_ids = '/'+this.id;
      //string = this.id;
      string=1;
    }else {
      parcel_ids = parcel_ids + "_" + this.id;
      //string = string + " " + this.id;
      string++;
    }
  });*/
 if (getCookie('multiUserMode') == '1'){
  $('.difUserIdHide').attr('disabled',true);
 }else {
   $('.difUserIdHide').attr('disabled',false);
   if (string != "empty") {
     $(".group-admin-view").attr('disabled', false);
     type = getCookie('parcel_elem_type');
     string = string + " ( " + type + " type)";
     $('.' + type + '_show').attr('disabled', false);
     if (type == "InSystem") {
       $('.gr_update_text').html('<span class="fa fa-eye"></span> View');
       $('.group-delete').attr('disabled', true);
     } else {
       $('.gr_update_text').html('<span class="glyphicon glyphicon-pencil"></span> Update')
     }
   } else {
     $('.InSystem_show,.Draft_show').attr('disabled', true);
     $(".group-admin-view").attr('disabled', true);
   }
 }

  $("#for_group_actions").html('<b>Checked parcels:</b> ' + string);
 // $(".group-admin-view").attr("href","/orderElement/group-view"+parcel_ids);
 // $(".group-update").attr("href","/orderElement/group-update"+parcel_ids);
 // $(".group-print").attr("href","/orderElement/group-print"+parcel_ids);
 // $(".group-print-advanced").attr("href","/orderElement/group-print-advanced"+parcel_ids);
 // $(".group-delete").attr("href","/orderElement/group-delete"+parcel_ids);
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
function init_collapse_buttons(){
    $("#collapse_filter").on("click", function(){
        $("#collapseTableOptions").collapse("hide");
    });
    $("#collapse_columns").on("click", function(){
        $("#collapse").collapse("hide");
    })
}
