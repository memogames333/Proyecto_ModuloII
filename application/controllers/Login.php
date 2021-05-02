<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('LoginModel',"login");
	}

	public function index()
	{
		$this->load->view('login');
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login', 'refresh');
	}

	//Iniciar sesion consultando los datos al servidor externo
	public function login()
	{
		//Recoger los datos por metodo POST
		$correo = $this->input->post("correo");
		$clave = $this->input->post("clave");
		if($this->login->exits_user($correo)){
			$row = $this->login->login_user($correo,$clave);
			if ($row) {
				if($row->activo==1){
					$user_session = array(
						'id_usuario'=>$row->id_usuario,
						'usuario'  => $row->usuario,
						'nombre'=>$row->nombre,
						'admin'=>$row->admin,
						'super_admin'=>$row->super_admin,
						'id_sucursal'=>$row->id_sucursal,
						'logged_in' => TRUE
					);
					$this->session->set_userdata($user_session);
					$data["type"] = "success";
					$data["title"] = "Aviso";
					$data["message"] = "Bienvenido ";
				}
				else{
					$data["type"] = "error";
					$data["title"] = "Error";
					$data["message"] = "El usuario esta inactivo!";
				}
			}
			else{
				$data["type"] = "error";
				$data["title"] = "Error";
				$data["message"] = "Contraseña incorrecta!";
			}
		}else{
			$data["type"] = "error";
			$data["title"] = "Error";
			$data["message"] = "El usuario ingresado no existe!";
		}

		//Se imprimen los datos
		echo json_encode($data);
	}

}
