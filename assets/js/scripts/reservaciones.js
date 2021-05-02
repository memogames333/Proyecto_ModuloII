var url = base_url+"reservaciones";
$(document).ready(function(){
  $(".fecha").datepicker({ dateFormat: 'yy-mm-dd' });
  $('.tel').mask('0000-0000');
  $(".numeric").numeric({
        negative: false,
        decimal: false
  });

  $("#enviar").click(function(event){
    event.preventDefault();
    let form = $("#form_add");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: url+'/agregar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        $("#divh").hide();
        $("#main_view").show();
        //notification(data.type,data.title,data.msg);
        if (data.type == "success") {
          toastr.success(data.msg);
          setTimeout(reload, 1500);
        }
        else {
          toastr.warning(data.msg);
          //$("#divh").hide();
          //$("#main_view").show();
          //$("#btn_add").removeAttr("disabled");
        }
      }
    });
  });

});

function reload() {
	location.href = base_url+"inicio";
}
