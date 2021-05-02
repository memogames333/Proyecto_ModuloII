<?php
include APPPATH . 'libraries/REST_Controller.php';
class Config extends REST_Controller {
	  /*
     * Get All Data from this method.
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->database();
       	$this->load->model('UtilsModel','utils');
    }

    public function index_get($id = 0)
  	{

        $row = $this->utils->getconfig();
        if ($row) {
          $data["id"] = 1;
          $data["costo_envio"] =  $row->costo_envio;
          $data["nombre_empresa"] = $nombre_empresa;
          $data["direccion_empresa"] =  $row->direccion_empresa;
          $data["telefono_empresa"] =  $row->telefono_empresa;
          $data["correo_empresa"] =  $row->correo_empresa;
          $data["logo_empresa"] =  $row->logo_empresa;
          $this->response($data, REST_Controller::HTTP_OK);
        }else{
          $data["type"] = "error";
        $this->response($data, REST_Controller::HTTP_UNAUTHORIZED);
        }
  }
}
