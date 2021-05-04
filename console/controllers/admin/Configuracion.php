<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Configuracion extends CI_Controller
{
	//Funcion index para enviar los datos de configuracion a la vista
	public function index()
	{
		validar_session_admin($this);
		$this->load->model('admin/Config_model');
		$this->load->helper('template_admin_helper');
		$rows = $this->Config_model->get_data();
		$nombre_empresa = $rows->nombre_empresa;
		$direccion_empresa = $rows->direccion_empresa;
		$telefono_empresa = $rows->telefono_empresa;
		$correo_empresa = $rows->correo_empresa;
		$web_empresa = $rows->web_empresa;
		$logo_empresa = $rows->logo_empresa;
		$data = array(
			'titulo' => "Configuración General",
			'urljs' => 'funciones_general.js',
			'nombre_empresa' => $nombre_empresa,
			'direccion_empresa' => $direccion_empresa,
			'telefono_empresa' => $telefono_empresa,
			'correo_empresa' => $correo_empresa,
			'web_empresa' => $web_empresa,
			'logo_empresa' => $logo_empresa,
		);
		$extras = array(
			'css' => array(
				0 => 'admin/libs/dropify/dropify.min.css'
			),
			'js' => array(
				0 => 'admin/libs/dropify/dropify.min.js'
			),
		);
		layout("admin/config/config",$data,$extras);
		//layout('admin/config/config',$data);
	}
	//Mediante la funcion cambios, guardamos los cambios enviados desde la vista
	function save_data(){

		$this->load->model('admin/Utils_model');
		$this->load->model('admin/Config_model');
		$this->load->helper('utilities_helper');
		$this->load->helper('upload_file_helper');
		$rows = $this->Config_model->get_data();
		$nombre = $this->input->post("nombre");
		$direccion = $this->input->post("direccion");
		$telefono = $this->input->post("telefono");
		$correo = $this->input->post("email");
		$web = $this->input->post("web");
		$path = "assets/admin/img/";

		if ($_FILES["logo"]["name"] != "") {
			$imagen = upload_image("logo",$path);
			$url=$path.$imagen;
		}
		else $url = $rows->logo_empresa;

		$form_data = array(
			"nombre_empresa"=>$nombre,
			"direccion_empresa"=>$direccion,
			"telefono_empresa"=>$telefono,
			"correo_empresa"=>$correo,
			"web_empresa"=>$web,
			"logo_empresa"=>$url,
		);
		$insertar = $this->Utils_model->_update("configuracion",$form_data,"id_configuracion=1");
		if($insertar){
			$xdatos["type"]="success";
			$xdatos["title"]="Alerta";
			$xdatos["msg"]="Datos Actualizados";
		}else
		{
			$xdatos["type"]="error";
			$xdatos["title"]="Alerta";
			$xdatos["msg"]="Error al actualizar los datos";
		}

		echo json_encode($xdatos);
	}

}
