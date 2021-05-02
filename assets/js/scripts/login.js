$(document).ready(function() {
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
