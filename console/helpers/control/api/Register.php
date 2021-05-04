<?php


include APPPATH . 'libraries/REST_Controller.php';

class Register extends REST_Controller {

	  /*
     * Get All Data from this method.
     * @return Response
    */
	private $table = "usuarios";
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
					$xdatos["id"] = $id_usuario;
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
		$this->load->library('email');
		$this->load->helper('email_helper');
		
		$correo= $this->input->post("correo");
		$nombre = $this->input->post("name");
		$pwd = base64_decode($this->input->post("pwd"));
		
	
		//echo $pwd." ".$correo;
		//$this->response(['Item created successfully.'], REST_Controller::HTTP_OK);
		$password = encrypt($pwd,'eNcRiPt_K3Y');
		
		if($this->login->exits_user($correo)==0){
			$code = encrypt(uniqid(),'eM4iL_K3Y');
			$code_exits = true;
			while ($code_exits==false){
				$exits_code = $this->login->verify_code($code);
				if($exits_code==1){
					$code_exits=true;
					$code = encrypt(uniqid(),'eM4iL_K3Y');
				}else{
					$code_exits=false;
				}
			}
			$user_data = array(
				'nombre'  => $nombre,
				'correo'  => $correo,
				'password'  => $password,
				'fecha_creacion'  => date("Y-m-d"),
				'hora_creacion'  => date("H:i:s"),
				'codigo'  => $code,
				'activo'=>0,
				'confirmado'=>0,
				'id_departamento'=>13,
				'id_municipio'=>263,
			);
			$this->utils->begin();
			$insert = $this->utils->insert($this->table,$user_data);
			if($insert){
				$email_data = array(
					"nombre" => $nombre,
					"codigo" => $code,
				);
				$this->email->from('info@opensolutionsystems.com', 'Mega Librería');
				$this->email->to($correo);
				$this->email->cc('guada6190@gmail.com');
				$this->email->bcc('them@their-example.com');
				$this->email->subject('Mega Librería- Confirmación de correo');
				$this->email->message(send_email($email_data));
				$this->email->set_mailtype('html');
				if ($this->email->send()) {
					$this->utils->commit();
					$xdatos["type"] = "success";
					$xdatos["title"] = "Exito";
					$xdatos["message"] = "Debes confirmar tu correo ";
					$this->response($xdatos, REST_Controller::HTTP_OK);
				} else {
					$this->utils->rollback();
					$xdatos["type"] = "error";
					$xdatos["title"] = "Error";
					$xdatos["message"] = "Problema al crear su cuenta!";
				}
			}else{
				$this->utils->rollback();
				$xdatos["type"] = "error";
				$xdatos["title"] = "Error";
				$xdatos["message"] = "Existen problemas, recarga la pagina!";
			}
		}else{
			$this->utils->rollback();
			$data["type"] = "error";
			$data["status"] ="CREDENCIALES INCORRECTAS";
			$data["message"] = "Ya existe un registro con este correo!";
			$this->response($data, REST_Controller::HTTP_UNAUTHORIZED);
		}
			
		//echo json_encode($xdatos);
		
}
    	

}
