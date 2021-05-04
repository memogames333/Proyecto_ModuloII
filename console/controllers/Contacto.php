<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contacto extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper("email_helper");
		$this->load->model("MenuModel","menu");

		//Do your magic here
	}

	public function index()
	{
		$extras = array(
			"css"=>array(),
			"js"=>array(
				0 => "js/funciones/contacto.js",
			),
		);
		template("contacto","",$extras);
	}

	function enviar(){
		if($this->input->method(TRUE)=="POST"){
			$this->load->library('email');
			$nombre = $this->input->post("nombre");
			$email = $this->input->post("email");
			$titulo = $this->input->post("titulo");
			$telefono = $this->input->post("telefono");
			$mensaje = $this->input->post("mensaje");
			$email_data = array(
				"nombre"=>$nombre,
				"email"=>$email,
				"titulo"=>$titulo,
				"telefono"=>$telefono,
				"mensaje"=>$mensaje
			);
			$row = $this->menu->get_settings();
			$correo_remitente = $row->correo_remitente;
			$this->email->from('info@opensolutionsystems.com', 'Mega Librería');
			$this->email->to($correo_remitente);
			//$this->email->cc('info@opensolutionsystems.com');
			//$this->email->bcc('guada6190@gmail.com');
			$this->email->subject('Mensaje de Contacto de Mega Libreria');
			$this->email->message(send_email_contact($email_data));
			$this->email->set_mailtype('html');
			if ($this->email->send()) {
				$xdatos["type"] = "success";
				$xdatos["title"] = "Exito";
				$xdatos["message"] = "Mensaje enviado con exito";
			} else {
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Problema al crear su cuenta!";
			}
			echo json_encode($xdatos);
		}
	}
}

/* End of file Contacto.php */
