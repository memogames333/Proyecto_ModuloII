<?php
include APPPATH . 'libraries/REST_Controller.php';
include APPPATH . 'libraries/Pagadito.php';
class OrdenTmp extends REST_Controller {

	  /*
     * Get All Data from this method.
     * @return Response
    */
	private $table = "orden";
	private $table_aux = "orden_detalle";
    public function __construct() {
       parent::__construct();
		$this->load->database();
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
		$this->load->model('OrdenesModel');
    }

    /*
     * Get All Data from this method.
     * @return Response
    */
	public function index_get($id_orden=""){
		$this->callPagadito($id_orden);
	}
    public function index_post(){
		$id_orden= $this->input->post("id_orden");
		$this->callPagadito($id_orden);
	}

	
	public function callPagadito($id_orden){
		
		define("UID", "bff2717bb2b635b67241befb9b8feadf");
		define("WSK", "9b54b194c5942533d8edb3f5805caf17");
		$Pagadito = new Pagadito(UID, WSK);
		

		$data = array();
		$this->load->model('OrdenesModel');
		$orden= $this->OrdenesModel->getOrden($id_orden);
		$detalleOrden= $this->OrdenesModel->getOrdenDetRows($id_orden);
		
		foreach ($orden as $ord) {
			$correo=$ord['correo'];
			$total=$ord['total'];
			$norden=$ord['numero_orden'];
			//echo $norden;
		}
	
		$data['orden'] = $orden;
		$data['detOrden'] = $detalleOrden;
		$data['orden'] = $orden;
		$data['detOrden'] = $detalleOrden;

		if ($Pagadito->connect())
		{
			$total=0;
			foreach ($detalleOrden as $items)
			{  
				$Pagadito->add_detail($items["cantidad"], $items["descripcion"], $items["precio"], $items["imagen"]);
				
			}
			$Pagadito->enable_pending_payments();
			
			$ern = $norden;
		
			if ($Pagadito->exec_trans($norden)) {
				 $url = $Pagadito->get_rs_value();
				 $xdatos['mensaje' ]= $Pagadito->get_rs_code().": ".$Pagadito->get_rs_message();
				 $xdatos['total' ] = $total;
				 $xdatos['url' ] = $url;
				 $xdatos['num_orden' ] = $norden;
				 $xdatos['id1' ] = $id_orden;
				$this->response($xdatos, REST_Controller::HTTP_OK);
												
			}
			else{
					switch($Pagadito->get_rs_code())
					{
							case "PG2001":
									/*Incomplete data*/
							case "PG3002":
									/*Error*/
							case "PG3003":
									/*Unregistered transaction*/
							case "PG3004":
									/*Match error*/
							case "PG3005":
									/*Disabled connection*/
							default:
							$mensaje="<div class=\"alert alert-danger\">
							<button class=\"close\" data-dismiss=\"alert\">×</button>
							<strong>Internal Error!</strong>".
							$Pagadito->get_rs_code().": ".$Pagadito->get_rs_message()."</div>";
							break;
					}
					$xdatos['mensaje' ]=$Pagadito->get_rs_code().": ".$Pagadito->get_rs_message();
					$xdatos['url' ] = 'https://megalibreria.com.sv/';
					$xdatos['total' ] = 0.0;
					$xdatos['num_orden' ] = $norden;
					$xdatos['id2' ] = $id_orden;
					$this->response($xdatos, REST_Controller::HTTP_OK);
				}
								
		}else {
					/*
					 * En caso de fallar la conexión, verificamos el error devuelto.
					 * Debido a que la API nos puede devolver diversos mensajes de
					 * respuesta, validamos el tipo de mensaje que nos devuelve.
					 */
					switch($Pagadito->get_rs_code())
					{
							case "PG2001":
									/*Incomplete data*/
							case "PG3001":
									/*Problem connection*/
							case "PG3002":
									/*Error*/
							case "PG3003":
									/*Unregistered transaction*/
							case "PG3005":
									/*Disabled connection*/
							case "PG3006":
									/*Exceeded*/
							default:
							$mensaje="<div class=\"alert alert-danger\">
							<button class=\"close\" data-dismiss=\"alert\">×</button>
							<strong>Connect Error!</strong>".
							$Pagadito->get_rs_code().": ".$Pagadito->get_rs_message()."</div>";
							break;
					}
					$xdatos['mensaje']=$Pagadito->get_rs_code().": ".$Pagadito->get_rs_message();
					$xdatos['url' ] = 'https://megalibreria.com.sv/';
					$xdatos['total' ] = 0.0;
					$xdatos['num_orden' ] = $norden;
					$xdatos['id3' ] = $id_orden;
					$this->response($xdatos, REST_Controller::HTTP_OK);
				}
		}
		
		
		
}
