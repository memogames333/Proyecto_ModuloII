<?php


include APPPATH . 'libraries/REST_Controller.php';

class Auth extends REST_Controller {

	  /*
     * Get All Data from this method.
     * @return Response
    */

    public function __construct() {

       parent::__construct();

       $this->load->database();
       	$this->load->model('LoginModel','login');
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');

    }

    /*
     * Get All Data from this method.
     * @return Response
    */
	public function index_get($correo = "",$pwd="")

	{
        if(!empty($correo) &&!empty($pwd)){

       //  $correo = $this->post("");
      
		$password = encrypt($pwd ,'eNcRiPt_K3Y');

		if($this->login->exits_user($correo)){
			$row = $this->login->login_user($correo,$password);
			if ($row) {
				$id = $row->id_usuario;
				$nombre = $row->nombre;
				$activo = $row->activo;
				$telefono = $row->telefono;
				$direccion = $row->direccion;
				$id_usuario = $row->id_usuario;
				if($activo==1){
					$user_session = array(
					   'id'  => $id_usuario,
						'nombre'  => $nombre,
						'telefono'  => $telefono,
						'direccion'  => $direccion,
						'idUsuario'  => $id_usuario,
						'correo'  => $correo,
						'loggedIn' => TRUE
					);
					$this->session->set_userdata($user_session);
					$xdatos["id"] = 0;
					$xdatos["idUsuario"] = $id_usuario;
					$xdatos["nombre"] = $nombre;
					$xdatos['telefono'] =  $telefono;
					$xdatos['direccion'] =  $direccion;
					$xdatos['correo' ] = $correo;
					$this->response($xdatos, REST_Controller::HTTP_OK);
				}
				else{
					$xdatos["type"] = "error";
					$xdatos["title"] = "Error";
					$xdatos["message"] = "El usuario esta inactivo!";
				}
			}
			else{
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Contraseña incorrecta!";
			}
		}else{
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "El usuario ingresado no existe!";
		}
        }else{

           $xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "El usuario ingresado no existe!";
        }
       

	}

    public function index_post(){
	
	
        //if(!empty($correo) &&!empty($pwd)){

	//$this->_post_args = $_POST;
      
     
		$correo= $this->input->post("correo");
		//$string = $this->input->post("correo");
		//$correo = base64_decode($string);
		$string= $this->input->post("pwd");
		$pwd = base64_decode($string);
      // $this->response(['Item created successfully.'], REST_Controller::HTTP_OK);
       //  $correo = $this->post("");
      
		$password = encrypt($pwd ,'eNcRiPt_K3Y');

		if($this->login->exits_user($correo)){
			$row = $this->login->login_user($correo,$password);
			if ($row) {
				$id = $row->id_usuario;
				$nombre = $row->nombre;
				$activo = $row->activo;
				$telefono = $row->telefono;
				$direccion = $row->direccion;
				$id_usuario = $row->id_usuario;
				if($activo==1){
					$user_session = array(
					   'id'  => $id_usuario,
						'nombre'  => $nombre,
						'telefono'  => $telefono,
						'direccion'  => $direccion,
						'idUsuario'  => $id_usuario,
						'correo'  => $correo,
						'loggedIn' => TRUE
					);
					$this->session->set_userdata($user_session);
					$xdatos["id"] = 0;
					$xdatos["idUsuario"] = $id_usuario;
					$xdatos["nombre"] = $nombre;
					$xdatos['telefono'] =  $telefono;
					$xdatos['direccion'] =  $direccion;
					$xdatos['correo' ] = $correo;
					$this->response($xdatos, REST_Controller::HTTP_OK);
				}
				else{
					$xdatos["type"] = "error";
					$xdatos["title"] = "Error";
					$xdatos["message"] = "El usuario esta inactivo!";
				}
			}
			else{
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Contraseña incorrecta!";
			}
		}
        
   
}
    	

}
