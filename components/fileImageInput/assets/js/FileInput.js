var file_api = ( window.File && window.FileReader && window.FileList && window.Blob ) ? true : false;

function init_file_prev(obj){
  el=obj.parent();
  obj.on('change',function(){
    var file_name;
    var input=this;
    var $this=$(input);
    var baze_img=$this.parent().find('.crop-image-upload-container');
    baze_img.find('img').remove();
    if( file_api && input.files[0] ){
      baze_img.find('.clear_photo').show();
      file_name = input.files[0].name;
      var file = input.files[0];
      if(baze_img){
        var reader = new FileReader();
        var img = document.createElement("img");
        img.file = file;
        baze_img
          .css('background','none')
          .append(img)
        reader.onload = (function(aImg) {
          return function(e) {
            aImg.src = e.target.result;
            aImg.longdesc= e.target.result
          };
        })(img);
        reader.readAsDataURL(file);
      }
    }else{
      file_name = $this.val();
    }
    /*if(!file_name.length){
     $this.parent().find('span.image_name').text($this.attr('default_text'))
     }else{
     $this.parent().find('span.image_name').text(file_name)
     }*/
  });
  var el=obj.parent();
  el.find('.clear_photo').click(function(){
    $el=$(this).parent().parent();
    $el.find('img').remove();
    $el.find('input').val('');
    $el.find('.help-block').html('');
    $(this).hide();
  });
}