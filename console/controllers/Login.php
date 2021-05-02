<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	private $table = "usuarios";

	public function __construct()
	{
		parent::__construct();
		$this->load->model('LoginModel','login');
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
		//Do your magic here
	}

	public function index()
	{
		if (isset($this->session->logged_in)){
			redirect("home","refresh");
		}else{
			$extras = array(
				'css' => array(),
				'js' => array(
					0 => "js/funciones/login.js"
				),
			);
			template("login/login","",$extras);
		}
	}

	public function init_session()
	{
		if (isset($this->session->logged_in)){
			redirect("checkout","refresh");
		}else{
			$extras = array(
				'css' => array(),
				'js' => array(
					0 => "js/funciones/login.js"
				),
			);
			template("login/login","",$extras);
		}
	}
	public function registro()
	{
		if (isset($this->session->logged_in)){
			redirect("checkout","refresh");
		}else{
			$extras = array(
				'css' => array(),
				'js' => array(
					0 => "js/funciones/login.js"
				),
			);
			template("login/registro","",$extras);
		}
	}

	function login(){
		$correo = $this->input->post("email");
		$password = encrypt($this->input->post("password"),'eNcRiPt_K3Y');

		if($this->login->exits_user($correo)){
			$row = $this->login->login_user($correo,$password);
			if ($row) {
				$nombre = $row->nombre;
				$apellido = $row->apellido;
				$activo = $row->activo;
				$telefono = $row->telefono;
				$direccion = $row->direccion;
				$id_usuario = $row->id_usuario;
				if($activo==1){
					$user_session = array(
						'nombre'  => $nombre,
						'apellido'  => $apellido,
						'telefono'  => $telefono,
						'direccion'  => $direccion,
						'id_usuario'  => $id_usuario,
						'logged_in' => TRUE
					);
					$this->session->set_userdata($user_session);
					$xdatos["cart"] = 0;
					if($this->cart->total_items() > 0)
					{
						$xdatos["cart"] = 1;
					}
					$xdatos["type"] = "success";
					$xdatos["title"] = "Exito";
					$xdatos["message"] = "Bienvenido ";
				}
				else{
					$xdatos["type"] = "error";
					$xdatos["title"] = "Error";
					$xdatos["message"] = "El usuario esta inactivo!";
				}
			}
			else{
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Contraseña incorrecta!";
			}
		}else{
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "El usuario ingresado no existe!";
		}
		echo json_encode($xdatos);
	}
	function get_code($length = 6)
	{
		$number = '0123456789';
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$numbersLength = strlen($number);
		$randomString = '';
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		for ($i = 0; $i < $length; $i++)
		{
				$randomString .= $number[rand(0, $numbersLength - 1)];
		}
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}
	function register(){
		$this->load->library('email');
		$this->load->helper('email_helper');
		$nombre = $this->input->post("name");
		$correo = $this->input->post("email");
		$apellido = $this->input->post("lastn");
		$password = encrypt($this->input->post("password"),'eNcRiPt_K3Y');

		if($this->login->exits_user($correo)==0){
			$code = encrypt(uniqid(),'eM4iL_K3Y');
			$code_exits = true;
			while ($code_exits==false){
				$exits_code = $this->login->verify_code($code);
				if($exits_code==1){
					$code_exits=true;
					$code = encrypt(uniqid(),'eM4iL_K3Y');
				}else{
					$code_exits=false;
				}
			}
			$codea = $this->get_code(6);
			$user_data = array(
				'nombre'  => $nombre,
				'apellido'  => $apellido,
				'correo'  => $correo,
				'password'  => $password,
				'fecha_creacion'  => date("Y-m-d"),
				'hora_creacion'  => date("H:i:s"),
				'codigo'  => $code,
				'activo'=>1,
				'confirmado'=>1,
				'id_municipio'=>263,
				'id_departamento'=>13,
				'ver_code'=>$codea,
			);
			$this->utils->begin();
			$insert = $this->utils->insert($this->table,$user_data);
			if($insert){
				$email_data = array(
					"nombre" => $nombre,
					"codigo" => $code,
					"code" => $codea,
				);
				/*$this->email->from('info@opensolutionsystems.com', 'Mega Librería');
				$this->email->to($correo);
				$this->email->bcc('guada6190@gmail.com');
				$this->email->subject('Mega Librería- Confirmación de correo');
				$this->email->message(send_email($email_data));
				$this->email->set_mailtype('html');
				/if ($this->email->send()) {*/
					$this->utils->commit();
					$xdatos["type"] = "success";
					$xdatos["title"] = "Exito";
					$xdatos["message"] = "Cuenta creada exitosamente";
					//$xdatos["message"] = "Debes confirmar tu correo ";
				/*} else {
					$this->utils->rollback();
					$xdatos["type"] = "error";
					$xdatos["title"] = "Error";
					$xdatos["message"] = "Problema al crear su cuenta!";
				}*/
			}else{
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Existen problemas, recarga la pagina!";
			}
		}else{
			$this->utils->rollback();
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "Ya existe un registro con este correo!";
		}
		echo json_encode($xdatos);
	}

	function logout(){
		$this->session->sess_destroy();
		redirect('login', 'refresh');
	}

	function cuenta_creada(){
		if($this->input->method(TRUE) == "GET"){

			template("login/account_created","","");

		}
	}

	function confirmar_correo($code=""){

		if($code==""){
			redirect("login","refresh");
		}
		else{
			$valido = $this->login->compare_code(trim($code));

			if($valido==1){
				$form_data = array(
					"confirmado"=>1,
					"activo"=>1
				);
				$where = " codigo='".$code."'";
				$this->utils->begin();
				$update = $this->utils->update($this->table,$form_data,$where);
				if($update){
					$this->utils->commit();
					$response = "valido";
				}else{
					$this->utils->rollback();
					$response = "invalido";
				}
			}else{
				$response = "invalido";
			}
			$extras = array(
				'css' => array(),
				'js' => array(
					0 => "js/funciones/login.js"
				),
			);
			$cart =0;
			if($this->cart->total_items() >0)
			{
					$cart =1;
			}
			$view_data = array(
				"response"=>$response,
				"cart" => $cart
			);
			template("login/confirmar",$view_data,$extras);
		}
	}

	//Envio de correo
	function forget(){
		if($this->input->method(TRUE) == "GET"){
			$extras = array(
				'css' => array(),
				'js' => array(
					0 => "js/funciones/login.js"
				),
			);
			template("login/change_password","",$extras);

		}
		else if($this->input->method(TRUE) == "POST"){

			$this->load->library('email');
			$this->load->helper('email_helper');

			$correo = $this->input->post("email");

			$row = $this->login->get_user_info($correo);
			$nombre = $row->nombre;
			$length = 4;
			$number = '0123456789';
			//$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$numbersLength = strlen($number);
			$randomString = '';
			$randomString .= $characters[rand(0, $charactersLength - 1)];
			for ($i = 0; $i < $length; $i++)
			{
				$randomString .= $number[rand(0, $numbersLength - 1)];
			}
			$randomString .= $characters[rand(0, $charactersLength - 1)];

			$code = $randomString;//encrypt(uniqid(),'eM4iL_K3Y');
			$code_exits = true;

			while ($code_exits==false){
				$exits_code = $this->login->verify_code_password($correo,$code);
				if($exits_code==1){
					$code_exits=true;
					$length = 4;
					$number = '0123456789';
					//$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$charactersLength = strlen($characters);
					$numbersLength = strlen($number);
					$randomString = '';
					$randomString .= $characters[rand(0, $charactersLength - 1)];
					for ($i = 0; $i < $length; $i++)
					{
						$randomString .= $number[rand(0, $numbersLength - 1)];
					}
					$randomString .= $characters[rand(0, $charactersLength - 1)];

					$code = $randomString;//encrypt(uniqid(),'eM4iL_K3Y');
			//		$code = encrypt(uniqid(),'eM4iL_K3Y');
				}else{
					$code_exits=false;
				}
			}

			$user_data = array(
				'change_pass_token'  => $code,
				'pass_token_used'=>0
			);
			$where = " correo='".$correo."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$user_data,$where);

			if($update){
				$email_data = array(
					"nombre" => $nombre,
					"codigo" => $code,
				);
				$this->email->from('info@opensolutionsystems.com', 'In5min');
				$this->email->to($correo);
				//$this->email->bcc('guada6190@gmail.com');
				$this->email->subject('In5min - Cambio de contraseña');
				$this->email->message(send_email_password($email_data));
				$this->email->set_mailtype('html');
				if ($this->email->send()) {
					$this->utils->commit();
					$xdatos["type"] = "success";
					$xdatos["title"] = "Exito";
					$xdatos["message"] = "Debes revisar tu correo eléctronico!.";
				} else {
					$this->utils->rollback();
					$xdatos["type"] = "error";
					$xdatos["title"] = "Error";
					$xdatos["message"] = "Problema al solicitar cambio de contraseñaa!";
				}
			}else{
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Existen problemas, recarga la pagina!";
			}
			echo json_encode($xdatos);
		}
	}

	//Verificacion de token
	function change_pasword($code){
		if(!isset($code)){
			redirect("login");
		}
		if($this->input->method(TRUE) == "GET"){
			$valido = $this->login->compare_code_password(trim($code));
			$id_usuario = "";
			if($valido==1){
				$form_data = array(
					"confirmado"=>1,
					"activo"=>1
				);
				$where = " change_pass_token='".$code."'";
				$this->utils->begin();
				$update = $this->utils->update($this->table,$form_data,$where);
				if($update){
					$rows = $this->login->get_id_usuario($code);
					$id_usuario = $rows->id_usuario;
					$token = $rows->pass_token_used;
					if($token==1){
						$this->utils->rollback();
						$response = "invalido";
					}else{
						$this->utils->commit();
						$response = "valido";
					}
				}else{
					$this->utils->rollback();
					$response = "invalido";
				}
			}else{
				$response = "invalido";
			}
			$extras = array(
				'css' => array(),
				'js' => array(
					0 => "js/funciones/login.js"
				),
			);
			$view_data = array(
				"response"=>$response,
				"id_usuario"=>$id_usuario,
			);
			template("login/change_password_new",$view_data,$extras);
		}
	}

	//Correo Enviado
	function change_password_send(){
		if($this->input->method(TRUE) == "GET"){

			template("login/change_password_mail","","");

		}
	}

	//Guardar contra
	function new_password(){
		if($this->input->method(TRUE) == "POST"){
			$pass  = $this->input->post("password_new");
			$id_usuario  = $this->input->post("id_usuario");
			$npass = encrypt($pass,"eNcRiPt_K3Y");
			$form = array(
				"password"=>$npass,
				"pass_token_used"=>1
			);
			$where = " id_usuario='".$id_usuario."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);
			if($update){
				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos["title"] = "Exito";
				$xdatos["message"] = "Contraseña Cambiada";
			} else {
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Error al cambiar la contraseña!";
			}
			echo json_encode($xdatos);
		}
	}

	//Finalizacion de cambio de password
	function change_password_success(){
		if($this->input->method(TRUE) == "GET"){

			template("login/change_password_success","","");

		}
	}

}

/* End of file Login.php */
