<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reservaciones extends CI_Controller{
  public function __construct()
	{
		parent::__construct();
		$this->load->model('ReservacionesModel',"reservaciones");
	}

	public function index()
	{
    $data = array(
			'clave' => 1,
		);
		layout('reservaciones',$data,'');
	}

  function agregar(){

		if($this->input->method(TRUE) == "POST"){

		  $nombre = $this->input->post("nombre");
			$telefono = $this->input->post("telefono");
      $fecha = $this->input->post("fecha");
			$numero_mesas = $this->input->post("numero_mesas");

      $data = array(
        "nombre"=>$nombre,
        "telefono"=>$telefono,
        "fecha"=>$fecha,
        "numero_mesas"=>$numero_mesas,
      );
      $respuesta = $this->db->insert("reservaciones",$data);
      if ($respuesta==1) {
        // code...
        $datax["type"] = "success";
        $datax["title"] = "Exito";
        $datax["msg"] = "Exito al solicitar reservacion";
      }
      else {
        // code...
        $datax["type"] = "error";
        $datax["title"] = "Error";
        $datax["msg"] = "Error al solicitar reservacion";
      }
      echo json_encode($datax);
		}
	}
}
?>
