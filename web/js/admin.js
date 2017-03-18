$(document).ready(function() {
  $(document)
    .on('click','.reload_on_click',function(){
      $.pjax.reload({container: '#crud-datatable-pjax'});
    })
    .on('click','.modal-header .close',function(){
      upd_but=$(this).closest('.modal-dialog ').find('.reload_on_click');
      if(upd_but.length>0){
        upd_but[0].click();
      }
    });
  ajax_send_admin_user_status_onchange();
});

function ajax_send_admin_user_status_onchange(){
  $( ".user_droplist" ).change(function() {
    elem = this;
   // elem.style.color = 'red';
    //index = Math.floor($('.lb-oz-tn-onChange').index(elemForm) /3);

    name = elem.name;
    usrStatus = 'none';
    id = name.substr(9,name.length-9);
    usrStatus = elem.value;
    $.ajax({
      type: 'POST',
      url: 'user/admin/update-status',
      data: { user_id: id, status: usrStatus},
      success: function(data) {
        if (data)  gritterAdd('Saving', 'Saving successful.', 'gritter-success');
        else gritterAdd('Error','Saving Error.','gritter-danger');
      },
      error:  function(xhr, str){
        gritterAdd('Error','Error: '+xhr.responseCode,'gritter-danger');
      }
    });
  });
}