<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cambiarcontra extends CI_Controller {

	private $table = "usuarios";

	public function __construct()
	{
		parent::__construct();
		$this->load->model("LoginModel", "login");
		$this->load->model("PerfilModel", "perfil");
		$this->load->model('UtilsModel', 'utils');
		$this->load->helper('utilities_helper');
		validate_profile($this);
		//Do your magic here
	}

	public function index()
	{
		$id_usuario = $this->session->id_usuario;
		$row = $this->perfil->user_info($id_usuario);
		$datos = array(
			"row" => $row,
			"url" => "cambiar_contra",
		);
		$dash = $this->load->view('perfil/dash_perfil', $datos, TRUE);
		$data = array(
			"id_usuario" => $id_usuario,
			"dash" => $dash,
		);
		$extras = array(
			'css' => array(
			),
			'js' => array(
				0 => "js/funciones/profile.js",
			),
		);
		template("perfil/cambiar_contra", $data, $extras);
	}

	function store(){
		$id_usuario = $this->session->id_usuario;
		$old_pass = encrypt($this->input->post("old_pass"),'eNcRiPt_K3Y');
		$password = encrypt($this->input->post("new_pass"),'eNcRiPt_K3Y');
		$same = $this->login->verify_password($old_pass,$id_usuario);
		if($same==1){
			$form = array(
				"password"=>$password
			);
			$where = " id_usuario='".$id_usuario."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);
			if ($update){
				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos["title"] = "Exito";
				$xdatos["message"] = "La contraseña ha sido cambiada exitosamente";
			}else{
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "La contraseña no se pudo cambiar!";
			}
		}else{
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "La contraseña antigua no es la misma!";
		}
		echo json_encode($xdatos);
	}

}

/* End of file Cambiarcontra.php */
