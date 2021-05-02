<?php


include APPPATH . 'libraries/REST_Controller.php';
//include APPPATH . 'libraries/Pagadito.php';
class Orden extends REST_Controller {

	  /*
     * Get All Data from this method.
     * @return Response
    */
	private $table = "orden";
	private $table_aux = "orden_detalle";
    public function __construct() {
       parent::__construct();

		$this->load->database();
       	$this->load->model('LoginModel','login');
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
		$this->load->model('OrdenesModel');
    }

    /*
     * Get All Data from this method.
     * @return Response
    */

    public function index_post(){

		$data = json_decode(file_get_contents('php://input'), TRUE);
		$this->utils->begin();
		$table = "orden";
		$table_aux = "orden_detalle";
		foreach ($data as $key => $val){
			$idOrden= $data[$key]['idOrden'];
			$form = array(
			'id_usuario' => $data[$key]['idUsuario'],
			'envio' => $data[$key]['envio'],
			'fecha' =>$data[$key]['fecha'],
		 	'hora' => $data[$key]['hora'],
		 	'tipo' => $data[$key]['tipo'],
			'entrega'=>$data[$key]['entrega'],
			'total' => $data[$key]['total'],
			'id_estado'=>1,
			);
			$length = 6;
			$number = '0123456789';
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$numbersLength = strlen($number);
			$randomString = '';
			$randomString .= $characters[rand(0, $charactersLength - 1)];
			for ($i = 0; $i < $length; $i++)
			{
				$randomString .= $number[rand(0, $numbersLength - 1)];
			}
			$randomString .= $characters[rand(0, $charactersLength - 1)];
			
			
				$insert = $this->utils->insert($table,$form);
	
			if($insert){
			
				$id_orden = $this->utils->insert_id();
				$caso = $randomString.$id_orden;
				$error = 0;
				foreach ($data[$key]['ordenDetalle'] as $key2 => $val2){	
					$form_aux = array(
					'id_orden' => $id_orden,
					'id_producto' => $data[$key]['ordenDetalle'][$key2]["idProducto"],
					'cantidad' =>$data[$key]['ordenDetalle'][$key2]["cantidad"],
					'precio' => $data[$key]['ordenDetalle'][$key2]["precio"],
					'subtotal' => $data[$key]['ordenDetalle'][$key2]["subtotal"],
					'fecha' => $data[$key]['ordenDetalle'][$key2]["fecha"],
					'hora' => $data[$key]['ordenDetalle'][$key2]["hora"],
					);
					$insert_aux = $this->utils->insert($table_aux, $form_aux);
					if(!$insert_aux){
						$error = 1;
					}
				}
				
				if(!$error){
					$form_up = array('numero_orden' => $caso);
					$where  = "id_orden='".$id_orden."'";
					
					$update = $this->utils->update($table, $form_up, $where);
					if($update){
						$xdatos["status"] = "Exito Orden:".$id_orden;
						$xdatos["error"] = $error;
						$xdatos["message"] ="Orden procesada";
						$this->utils->commit();
						$orden_actual=$this->OrdenesModel->getOrden($id_orden);
						foreach ($orden_actual as $ord) {
							$idOrden=$ord['id_orden'];
							$nOrden=$ord['numero_orden'];			
						}			
						
						$data['idOrden'] = $id_orden;
						$data['numeroOrden'] = $nOrden;
						
						$this->sendmaill($id_orden);
						$this->response($data, REST_Controller::HTTP_OK);
						
					}
				}
				else{
					$this->utils->rollback();
					$xdatos["status"] = "Error";
					$xdatos["message"] = "No actualizo numero de orden";
					$this->response($data, REST_Controller::HTTP_UNAUTHORIZED);
				}
					
				
			}	
		
	
	}
		
		
	}

	public function sendmaill($id_orden){
		//require_once(APPPATH.''); 
		$data = array();
				$this->load->model('OrdenesModel');
				$orden= $this->OrdenesModel->getOrden($id_orden);
				$detalleOrden= $this->OrdenesModel->getOrdenDetRows($id_orden);
				
				foreach ($orden as $ord) {
					$correo=$ord['correo'];
					$total=$ord['total'];
					$norden=$ord['numero_orden'];
				}
			
				$data['orden'] = $orden;
				$data['detOrden'] = $detalleOrden;
					$data['orden'] = $orden;
		$data['detOrden'] = $detalleOrden;

		$head='Mega Librería- Confirmación de compra, ORDEN N°'.$norden;
		$body = $this->load->view('checkout/checkout_bill2',$data,true);

		$this->load->library('email');
		$this->load->helper('email_helper');
		$this->email->set_mailtype('html');

		$this->email->from('info@tumundolaboral.com.sv', 'Mega Librería');
		$this->email->cc('info@opensolutionsystems.com', 'Mega Librería');
		$this->email->bcc('luis.aguilar@ues.edu.sv');
		$this->email->to($correo);
		$this->email->subject($head);
		$this->email->message($body);
		$this->email->send();
				
		}
}
