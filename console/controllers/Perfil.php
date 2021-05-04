<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {

	private $table = "usuarios";

	public function __construct()
	{
		parent::__construct();
		$this->load->model("PerfilModel","perfil");
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
		validate_profile($this);
		//Do your magic here
	}

	public function index()
	{

		$id_usuario = $this->session->id_usuario;
		$row = $this->perfil->user_info($id_usuario);
		$datos = array(
			"row"=>$row,
			"url"=>"perfil",
		);
		$dash = $this->load->view('perfil/dash_perfil', $datos, TRUE);
		$municipios = $this->perfil->get_municipios($row->id_departamento);
		$departamentos = $this->perfil->get_departamentos();

		$data = array(
			"id_usuario"=>$id_usuario,
			"dash"=>$dash,
			"row"=>$row,
			"municipios"=>$municipios,
			"departamentos"=>$departamentos,

		);
		$extras = array(
			'css' => array(
				0 => "libs/select2/select2.min.css"
			),
			'js' => array(
				0 => "libs/select2/select2.min.js",
				1 => "js/funciones/profile.js"
			),
		);
		template("perfil/perfil",$data,$extras);
	}

	function store(){
		$id_usuario = $this->input->post("id_usuario");
		$nombre = $this->input->post("nombre");
		$telefono = $this->input->post("telefono");
		$direccion = $this->input->post("direccion");
		$id_departamento = $this->input->post("id_departamento");
		$id_municipio = $this->input->post("id_municipio");

		$user_data = array(
			'nombre'  => $nombre,
			'telefono'  => $telefono,
			'direccion'  => $direccion,
			'id_departamento'  => $id_departamento,
			'id_municipio'  => $id_municipio,
		);
		$where = " id_usuario='".$id_usuario."'";
		$this->utils->begin();
		$insert = $this->utils->update($this->table,$user_data,$where);
		if($insert){
			$this->session->set_userdata('nombre', $nombre);
			$this->utils->commit();
			$xdatos["type"] = "success";
			$xdatos["title"] = "Exito";
			$xdatos["message"] = "Datos editados correctamente";
		}else{
			$this->utils->rollback();
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "Existen problemas, recarga la pagina!";
		}
		echo json_encode($xdatos);
	}

	function get_municipios()
	{
		$id_departamento = $this->input->post("id_departamento");
		$municipios = $this->perfil->get_municipios($id_departamento);
		$option = "<br>";
		$option .= "<option value='0'>Seleccione un municipio</option>";
		foreach ($municipios as  $value) {
			$option .= "<option value='".$value->id_municipio."'>".$value->nombre_municipio."</option>";
		}
		echo $option;
	}
}

/* End of file Perfil.php */
