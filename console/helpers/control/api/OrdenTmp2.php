<?php
include APPPATH . 'libraries/REST_Controller.php';

class OrdenTmp2 extends REST_Controller {

	  /*
     * Get All Data from this method.
     * @return Response
    */
	
    public function __construct() {
       parent::__construct();
		$this->load->database(); 
		$this->load->model('OrdenesModel');
    }

    /*
     * Get All Data from this method.
     * @return Response
    */
	public function index_get($id_orden){
		$this->paga($id_orden);
	}
	public function index_post(){
		$id_orden= $this->input->post("id_orden");
		$this->paga($id_orden);
	}
	
	public function paga($id_orden){
		$this->load->database(); 
		$this->load->model('OrdenesModel');
		$data = array();
		$orden= $this->OrdenesModel->getOrden($id_orden);
		$detalleOrden= $this->OrdenesModel->getOrdenDetRows($id_orden);
		
		
		foreach ($orden as $ord) {
			$correo=$ord['correo'];
			$total=$ord['total'];
			$norden=$ord['numero_orden'];
		}
	
		
		$xdatos['id'] = $id_orden;
		$xdatos['num_orden'] = $norden;
		$xdatos['total'] = $total;
		$xdatos['mensaje'] = "devolver orden post!";
		
		$this->response($xdatos, REST_Controller::HTTP_OK);

		
		}		
}
