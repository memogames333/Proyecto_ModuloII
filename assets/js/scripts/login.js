$(document).ready(function() {
  $("#enviar").click(function(event){
    event.preventDefault();
    let form = $("#form_add");
    let formdata = false;
    if (window.FormData) {
      formdata = new FormData(form[0]);
    }
    $.ajax({
      type: 'POST',
      url: base_url+'login/agregar',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (data) {
        //$("#divh").hide();
        //$("#main_view").show();
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

  $("#correo").keyup(function (event) {
    if($(this).val()!="")
    {
      if(event.keyCode == 13)
      {
        $("#clave").focus();
      }
    }
  });
  $("#clave").keyup(function (event) {
    if($(this).val()!="")
    {
      if(event.keyCode == 13)
      {
		  login();
      }
    }
  });
});
$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btn_ini_sesion", function(event) {
	  login();
  });
});

function login(){
    var correo = $("#correo").val();
    var clave = $("#clave").val();
    let token = $("#csrf_token_id").val()
  $.ajax({
    type: 'POST',
    url: base_url+"login/login",
    data: "correo="+correo+"&clave="+clave+"&csrf_test_name="+token,
    dataType: 'JSON',
    success: function(datax) {
    	if(datax.type=="success"){

			Swal.fire({
				title: datax.title,
				type: datax.type,
				text: datax.message,
				showCancelButton: false,
				confirmButtonColor: '#283593',
				confirmButtonText: 'Continuar',
			}).then((result) => {
				setTimeout("reload()",500);
			});
		}else {
			Swal.fire({
				title: datax.title,
				type: datax.type,
				text: datax.message,
				showCancelButton: false,
				confirmButtonColor: '#a40110',
				confirmButtonText: 'OK',
			});
		}
    }
  });
}
function reload() {
	location.href = base_url;
}
