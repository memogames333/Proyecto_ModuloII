<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Edicion_pagina extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		validar_session($this);
		$this->load->model('admin/Config_model','edit');
		$this->load->helper('template_admin_helper');
		$this->load->model('UtilsModel',"utils");
		$this->load->helper('utilities_helper');
		//Do your magic here
	}

	//Funcion index para enviar los datos de configuracion a la vista
	public function index()
	{
		$rows = $this->edit->get_data_sitio();
		$data = array(
			"row"=>$rows,
		);
		$extras = array(
			"css"=>array(
				0 => 'admin/libs/dropify/dropify.min.css'
			),
			"js"=>array(
				0 => 'admin/libs/dropify/dropify.min.js',
				1 => 'admin/js/funciones/funciones_edicion_pagina.js',
			),
		);
		layout('admin/page_edit/page_config',$data,$extras);
	}
	//Mediante la funcion cambios, guardamos los cambios enviados desde la vista
	public function cambios(){
		$nombre = $_POST["nombre"];
		$telefono = $_POST["telefono"];
		$correo = $_POST["correo"];
		$correo_remitente = $_POST["correo_remitente"];
		$facebook = $_POST["facebook"];
		$twitter = $_POST["twitter"];
		$instagram = $_POST["instagram"];
		$rows = $this->edit->get_data_sitio();


		//Verificamos que la imagen sea distinta
		$logo = $rows->logo;
		$imagen = $rows->imagen;
		if ($_FILES["logo"]["name"] != "") {

			//Configuracion para los valores de la imagen de subir
			$_FILES['file']['name'] = $_FILES['logo']['name'];
			$_FILES['file']['type'] = $_FILES['logo']['type'];
			$_FILES['file']['tmp_name'] = $_FILES['logo']['tmp_name'];
			$_FILES['file']['error'] = $_FILES['logo']['error'];
			$_FILES['file']['size'] = $_FILES['logo']['size'];
			$config['upload_path'] = "./assets/img/";
			$config['allowed_types'] = 'jpg|jpeg|png|bmp';
			$info = new SplFileInfo( $_FILES['logo']['name']);
			$name = uniqid(date("dmYHi")).".".$info->getExtension();
			$config['file_name'] = $name;
			$this->upload->initialize($config);
			$this->load->library('upload', $config);

			//Subimos la imagen al servidor
			if ($this->upload->do_upload('file')){
				$logo = 'assets/img/'.$name;
			}
		}
	//	$imagen = "";
		if ($_FILES["bienvenida"]["name"] != "") {

			//Configuracion para los valores de la imagen de subir
			$_FILES['file']['name'] = $_FILES['bienvenida']['name'];
			$_FILES['file']['type'] = $_FILES['bienvenida']['type'];
			$_FILES['file']['tmp_name'] = $_FILES['bienvenida']['tmp_name'];
			$_FILES['file']['error'] = $_FILES['bienvenida']['error'];
			$_FILES['file']['size'] = $_FILES['bienvenida']['size'];
			$config['upload_path'] = "./assets/img/";
			$config['allowed_types'] = 'jpg|jpeg|png|bmp';
			$info = new SplFileInfo( $_FILES['bienvenida']['name']);
			$name = uniqid(date("dmYHi")).".".$info->getExtension();
			$config['file_name'] = $name;
			$this->upload->initialize($config);
			$this->load->library('upload', $config);

			//Subimos la imagen al servidor
			if ($this->upload->do_upload('file')){
				$imagen = 'assets/img/'.$name;
			}
		}
		//Si en caso no viene imagen, se guardan los cambios

			$form_data = array(
				"nombre"=>$nombre,
				"telefono"=>$telefono,
				"correo"=>$correo,
				"correo_remitente"=>$correo_remitente,
				"facebook"=>$facebook,
				"twitter"=>$twitter,
				"instagram"=>$instagram,
				"logo"=>$logo,
				"imagen"=>$imagen,
			);
			$where = "id_sitio=1";
			$this->utils->begin();
			$insertar = $this->utils->update("sitio_web",$form_data,$where);
			if($insertar){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo editado correctamente!";
			}
			else
			{
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al editar el registro!";
			}

		echo json_encode($xdatos);
	}

}
