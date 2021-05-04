<?php
include APPPATH . 'libraries/REST_Controller.php';
class ProductoDestacado extends REST_Controller {
    public function __construct() {
	    parent::__construct();
	    $this->load->database();
		$this->load->model("MenuModel","menu");
	}

	public function index_get(){
        $data = $this->menu->get_productos_destacados();
        $this->response($data, REST_Controller::HTTP_OK);
	}
}
