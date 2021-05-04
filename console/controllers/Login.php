<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
*@Company Open Solutions Systems
*@Author Jesus Sagastume
*Codeigniter 3.1.11
*Inspinia 2.7 Template
*/

class Login extends CI_Controller
{
	//Se cargan los modelos
	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/LoginModel',"login");
		$this->load->model('UtilsModel');
		$this->load->helper('utilities_helper');
	}
	
	//Redireccionamos a la vista
	public function index()
	{
		if(isset($this->session->admin)){
			redirect("admin/dashboard","refresh");
		}
		$this->load->view('admin/login');
	}

	//Se destruye la sesion
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('admin/login', 'refresh');
	}

	//Iniciar sesion consultando los datos al servidor externo
	public function login()
	{
		//Recoger los datos por metodo POST
		$correo = $this->input->post("correo");
		$clave = $this->input->post("clave");

		if($this->login->exits_user($correo)){
			$row = $this->login->login_user($correo,encrypt($clave,"eNcRiPt_K3Y"));
			if ($row) {
				$nombre = $row->nombre;
				$usuario = $row->usuario;
				$activo = $row->activo;
				$id_usuario = $row->id_usuario;
				$admin = $row->admin;
				if($activo==1){
					$user_session = array(
						'id_usuario'=>$id_usuario,
						'usuario'  => $usuario,
						'nombre'=>$nombre,
						'admin'=>$admin,
						'logged_in_admin' => TRUE
					);
					$this->session->set_userdata($user_session);
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

		//Se imprimen los datos
		echo json_encode($xdatos);
	}

}
