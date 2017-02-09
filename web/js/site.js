$(document).ready(function() {

  count=0;
  $(".add_new_row").click(function () {
    var table = document.getElementById("itemTable");
    tr=$('.itemTable tr').length;
    var row = table.insertRow(count);
count++;

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    cell1.innerHTML = "Number"+count;
    cell2.innerHTML = "Price"+count;
    cell3.innerHTML = "Total"+count;
    cell4.innerHTML = "Delete"+count;


  });




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