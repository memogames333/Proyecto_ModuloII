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
	public function nuevo_usuario()
	{
		$this->load->view('nuevo_usuario');
	}
	public function admin()
	{
		$this->load->view('admin_usuario');
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
	function agregar(){

		if($this->input->method(TRUE) == "POST"){

		  $usuario = $this->input->post("usuario");
			$password = $this->input->post("password");
			$tipo = $this->input->post("tipoUsuario");
			//$numero_mesas = $this->input->post("numero_mesas");

      $data = array(
        "usuario"=>$usuario,
        "password"=>md5($password),
				"tipo"=>$tipo,
				"activo"=>"1",
      );
      $respuesta = $this->db->insert("usuario",$data);
      if ($respuesta==1) {
        // code...
        $datax["type"] = "success";
        $datax["title"] = "Exito";
        $datax["msg"] = "Exito al insertar usuario";
      }
      else {
        // code...
        $datax["type"] = "error";
        $datax["title"] = "Error";
        $datax["msg"] = "Error al insertar usuario";
      }
      echo json_encode($datax);
		}
	}
}
