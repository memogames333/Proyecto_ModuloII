function getRandomArbitrary(min, max) {
  return Math.random() * (max - min) + min;
}
function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
}
let random = getRandomInt(10000000, 99999999);

function md5key(value) {
    return md5(value);
}
var md5key = md5key(random);

$(document).ready(function()
{
  $('#formulario1A').validate({

  		rules: {
  			nombre:
  			{
  				required: true,
  			},
  			apellido:
  			{
  				required: true,
  			},
  			mail:
  			{
  				required: true,
          email: true
  			},
        password:
  			{
  				required: true,
          minlength: 8,
  			},
  			confir_password: {
          required: true,
  				equalTo: "#password"
  			},
  		},
  		messages:
  		{
  			password:
        {
          required: "Por favor ingrese la Contraseña",
          minlength: "Ingrese como minimo 8 caracteres",
        },
  			confir_password: "Las contraseñas deben coincidir",
  			mail: {
  			  required: "Por favor ingrese el correo electrónico",
  				email: "Por favor, introduce una dirección de correo electrónico válida.",
  			},
        nombre: "Ingrese su nombre",
        apellido: "Ingrese su apellido",
  		},
  		submitHandler: function (form)
  		{
  			insert();
  		}
  	});

    $('#formularioLog').validate({

    		rules: {
    			email:
    			{
    				required: true,
            email: true
    			},
          password:
    			{
    				required: true,
    			},
    		},
    		messages:
    		{
    			password:
          {
            required: "Por favor ingrese la Contraseña",
          },
    			email: {
    			  required: "Por favor ingrese el correo electrónico",
    				email: "Por favor, introduce una dirección de correo electrónico válida.",
    			},
    		},
    		submitHandler: function (form)
    		{
    			login();
    		}
    	});
})

$(document).on("change", "#departamento",function(event){
	var id_estado = $(this).val();
	let token = $("#csrf_token_id").val();
	$.ajax({
		type: "POST",
		url: base_url+"registro/traer_ciudad",
		data: "id_estado=" + id_estado +"&csrf_test_name="+token,
		dataType: "JSON",
		success: function(datax) {
			if (datax.typeinfo == "Success") {
				$("#municipio").html(datax.opt);
			}
		}
	});
});

function insert()
{
  let token = $("#csrf_token_id").val();
  var nombre = $("#nombre").val();
  var apellido = $("#apellido").val();
  var telefono = $("#telefono").val();
  var departamento = $("#departamento").val();
  var municipio = $("#municipio").val();
  var direccion = $("#direccion").val();
  var mail = $("#mail").val();
  var password = $("#password").val();

  let encryption = new Encryption();
  var nonceValue=	md5key;
	mail = encryption.encrypt(mail,nonceValue);
	password = encryption.encrypt(password,nonceValue);

  var dataString = "nombre="+nombre;
  dataString += "&apellido="+apellido;
  dataString += "&telefono="+telefono;
  dataString += "&departamento="+departamento;
  dataString += "&municipio="+municipio;
  dataString += "&direccion="+direccion;
  dataString += "&mail="+mail;
  dataString += "&password="+password;
  dataString += "&csrf_test_name="+token+"&JeRDAJeaRRgd="+md5key;

  $.ajax({
		type: "POST",
		url: base_url+"registro/registro",
		data: dataString,
		dataType: "JSON",
		success: function(datax) {
			if (datax.typeinfo == "Success")
      {
        swal({
          title: "Aviso",
          text: "Registro exitoso!",
          type: "success",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Aceptar",
          // cancelButtonText:"Continuar" ,
          closeOnConfirm: true,
          closeOnCancel: false
          },
          function(isConfirm) {
          if (isConfirm) {
            var url1=base_url+'login';
             window.location =url1;
          }
        });
			}
		}
	});
}

// $(document).on("click", "#btn_ini_sesion", function(event) {
//   login();
// });

function login()
{
  let encryption = new Encryption();
  var nonceValue=	md5key;
  if($("#email").val() != "")
  {
    if($("#password").val() != "")
    {
      var correo = encryption.encrypt($("#email").val(),nonceValue);
      var clave = encryption.encrypt($("#password").val(),nonceValue);
      let token = $("#csrf_token_id").val();
      $.ajax({
        type: 'POST',
        url: base_url+"login/login",
        data: "correo="+correo+"&clave="+clave+"&csrf_test_name="+token+"&JeRDAJeaRRgd="+md5key,
        dataType: 'JSON',
        // beforeSend: function(){
    		// 	// Show image container
    		// 	$(".caja_cargando").attr("hidden", false);
        //   $('body').css("overflow", "hidden");
    		// },
        success: function(datax) {
        	if(datax.typeinfo=="Success")
          {
            setTimeout("reload()",500);
    		  }
          else
          {
            // id_asp = datax.id;
            // notification(datax.title,datax.msg,"izquierda",datax.typeinfo);
      		}
        },
        // complete:function(data){
    		// 	// Hide image container
    		// 	$(".caja_cargando").attr("hidden", true);
        //   $('body').removeAttr("style");
    		// }
      });
    }
    else
    {
      notification("Informacion","Debe de ingresar su contraseña","izquierda","Info");
    }
  }
  else
  {
    notification("Informacion","Debe de ingresar su correo electronico","izquierda","Info");
  }
}

function reload() {
	location.href = base_url;
}
