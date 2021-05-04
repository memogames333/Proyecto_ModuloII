<?php

include APPPATH . 'libraries/Pagadito.php';
class Checkout extends CI_Controller{
	private $table = "usuarios";

	function __construct() {
		parent::__construct();

		//load post model
		$this->load->model('Categorias_model');
		$this->load->model('Departamento_model');
		$this->load->model("PerfilModel","perfil");
		$this->load->model('UtilsModel','utils');
		$this->load->model('OrdenesModel');
		$this->load->helper('utilities_helper');
	}

	public function index(){
		$data = array();
		// Get record count
		$conditions['returnType'] = 'count';
		$data['departamentos'] = $this->Departamento_model->getRows($conditions);
		$this->clean_session();

		if (!isset($this->session->logged_in)){
			redirect("login","refresh");
		}else{
			$id_usuario = $this->session->id_usuario;
			$row = $this->perfil->user_info($id_usuario);
			$datos = array(
				"row"=>$row,
				"url"=>"perfil",
			);
			$municipios = $this->perfil->get_municipios($row->id_departamento);
			$departamentos = $this->perfil->get_departamentos();
			$total = $this->cart->total();
			$id_mun = $row->id_municipio;
			$porcentaje_envio = $this->utils->getPorcenaje($total,$id_mun);
			$costo_envio = $this->utils->getenvio();
			$costo = $costo_envio->costo_envio;
			if($porcentaje_envio != 0)
			{
				$porcentaje = round($porcentaje_envio->cliente/100,2);
				$costo = round($costo * $porcentaje,2);
			}
			// Load the list page view
			$data = array(
				"id_usuario"=>$id_usuario,
				"row"=>$row,
				"municipios"=>$municipios,
				"departamentos"=>$departamentos,

			);
			$extras = array(
				'css' => array(
					0 => "libs/select2/select2.min.css"
				),
				'js' => array(
					0 => "libs/mask/jquery.mask.js",
					1 => "libs/select2/select2.min.js",
					2 => "js/funciones/checkout.js"
				),
			);
			//	template("perfil/perfil",$data,$extras);
			template("checkout/checkout_address",$data,$extras);
		}
	}

	function store(){

		$id_usuario = $this->input->post("id_usuario");
		$nombre = $this->input->post("nombre");
		$telefono = $this->input->post("telefono");
		$direccion = $this->input->post("direccion");
		$id_departamento = $this->input->post("id_departamento");
		$id_municipio = $this->input->post("id_municipio");

		$user_data = array(
			'nombre'  => $nombre,
			'telefono'  => $telefono,
			'direccion'  => $direccion,
			'id_departamento'  => $id_departamento,
			'id_municipio'  => $id_municipio,
		);
		$where = " id_usuario='".$id_usuario."'";
		$this->utils->begin();
		$insert = $this->utils->update($this->table,$user_data,$where);
		if($insert){
			$this->session->set_userdata('nombre', $nombre);
			$this->utils->commit();
			$xdatos["type"] = "success";
			$xdatos["title"] = "Exito";
			$xdatos["message"] = "Datos editados correctamente";
		}else{
			$this->utils->rollback();
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "Existen problemas, recarga la pagina!";
		}
		echo json_encode($xdatos);
	}

	function get_municipios()
	{
		$id_departamento = $this->input->post("id_departamento");
		$municipios = $this->perfil->get_municipios($id_departamento);
		$option = "";
		$option .= "<option value='0'>Seleccione un municipio</option>";
		foreach ($municipios as  $value) {
			$option .= "<option value='".$value->id_municipio."'>".$value->nombre_municipio."</option>";
		}
		echo $option;
	}



	public function shipping(){
		$id_usuario = $this->session->id_usuario;
		$envio = $this->session->envio;
		//$this->load->model('UtilsModel');
		$total = $this->cart->total();
		$idcli = $this->session->id_usuario;
		$cliente = $this->utils->getIdMunCli($idcli);
		$id_mun = $cliente["id_municipio"];
		$porcentaje_envio = $this->utils->getPorcenaje($total,$id_mun);
		$costo_envio = $this->utils->getenvio();

		$costo = $costo_envio->costo_envio;
		if($porcentaje_envio != 0)
		{
			$porcentaje = round($porcentaje_envio->cliente/100,2);
			$costo = round($costo * $porcentaje,2);
		}
		//$data = array();
		$row = $this->perfil->user_info($id_usuario);
		$data = array(
			"id_usuario"=>$id_usuario,
			"row"=>$row,
			"costo"=>$costo,
			"envio"=>$costo,
		);
		template("checkout/checkout_shipping",$data,"");
	}
	public function payment(){
		$extras = array(
			'css' => array(
				0 => "libs/select2/select2.min.css",
				1 => 'admin/libs/dropify/dropify.min.css',
				2 => 'admin/libs/izitoast/iziToast.min.css'
			),
			'js' => array(
				0 => "libs/select2/select2.min.js",
				1 => "js/funciones/checkout.js",
				2 => 'admin/libs/dropify/dropify.min.js',
				3 => 'admin/libs/izitoast/iziToast.min.js'
			),
		);
		$envio = $this->session->envio;
		$this->load->model('UtilsModel');
		$costo_envio = $this->utils->getenvio();
		$data = array('envio' => $envio, 'extras'=>$costo_envio);
		template("checkout/checkout_payment",$data,$extras);
	}
	public function review($token="",$recibo="")
	{
		$this->load->model("UtilsModel");

		$data = array();
		$data["estate_payment"] = "";
		if($token != "")
		{
			define("UID", "bff2717bb2b635b67241befb9b8feadf");
			define("WSK", "9b54b194c5942533d8edb3f5805caf17");

			$Pagadito = new Pagadito(UID, WSK);

			if ($Pagadito->connect()) {
			if ($Pagadito->get_status($token)) {
			 $estado = $Pagadito->get_rs_status();
			 if ($estado == "COMPLETED")
			 {
			   $data["payment_ref"] = $Pagadito->get_rs_reference();
			   $fecha_cobro = $Pagadito->get_rs_date_trans();
			   $ref=$data["payment_ref"];
			   list($fechaap,$hora) = explode(" ",$fecha_cobro);
			   $data["fecha_payment"] = d_m_Y($fechaap)." ".hora_A_P($hora);
				 $data["estate_payment"] = "COMPLETADO";
			   $data["message_payment"] = "Pago exitoso!";
				 $dataup = array(
					 'referencia' => $ref,
					 'token' => $token,
					 'pagado' => 1,
					 'fecha_aprobacion' => $fechaap,
				 );
				 $where = "numero_orden = '".$recibo."'";
				 $this->utils->update("orden",$dataup,$where);
			 }
			 else
			 {
				 $data["estate_payment"] = "ERROR";
			   $data["message_payment"] = $Pagadito->get_rs_code().": ".$Pagadito->get_rs_message();
			 }
			}
			else
			{
				$data["estate_payment"] = "NO STATUS";
				$data["message_payment"] = $Pagadito->get_rs_code().": ".$Pagadito->get_rs_message();
			}
			}
			else
			{
			  $$data["estate_payment"] = "ERROR";
				$data["message_payment"] = $Pagadito->get_rs_code().": ".$Pagadito->get_rs_message();
			}
		}

		$envio = $this->session->envio;
		$norden=$this->session->norden;
		$id_orden=$this->OrdenesModel->getIdOrden($norden);

		$orden= $this->OrdenesModel->getOrden($id_orden);
		$detalleOrden= $this->OrdenesModel->getOrdenDetRows($id_orden);
		//echo $id_orden;
		//print_r($detalleOrden);
		$extras = array(
			'css' => array(
				0 => "libs/select2/select2.min.css"
			),
			'js' => array(
				0 => "libs/select2/select2.min.js",
				1 => "js/funciones/checkout.js"
			),
		);
		//  $data = array('envio' => $envio);
		$data['envio']= array('envio' => $envio);
		$data['orden'] = $orden;
		$data['detOrden'] = $detalleOrden;
		template("checkout/checkout_review",$data,$extras);
	}
	public function guardar_orden(){
		$id_cliente = $this->input->post("id_cliente");
		$total_envio = $this->input->post("total_envio");
		$entrega = $this->input->post("entrega");
		if (isset($this->session->logged_in)){
			$id_usuario = $this->session->id_usuario;
		}
		$this->session->envio = $total_envio;
		$this->session->entrega = $entrega;
	}
	public function grabar_orden(){
		$this->load->model("UtilsModel");
		$this->load->model("ProductoModel","productos");
		$this->load->helper('upload_file_helper');
		$efectivo = $this->input->post("efectivo");
		$tarjeta = $this->input->post("tarjeta");
		$transferencia = $this->input->post("transferencia");

		$upload_path = "assets/img/comprobantes/";

		$path = "img/comprobantes/";

		if ($_FILES["foto"]["name"] != "") {
			$imagen = upload_image("foto",$upload_path);
			//resize_image($imagen, $upload_path,1000,1000,100,0,"");
			$up_img1=$path.$imagen;
		}
		else{
			$up_img1 = "";
		}


		$total = $this->cart->total();
		$total_envio = $this->session->envio;
		$idcli = $this->session->id_usuario;
		$cliente = $this->utils->getIdMunCli($idcli);
		$id_mun = $cliente["id_municipio"];
		$porcentaje_envio = $this->utils->getPorcenaje($total,$id_mun);
		$costo_envio = $this->utils->getenvio();
		$total_envio = $costo_envio->costo_envio;
		if($porcentaje_envio != 0)
		{
			$porcentaje_cliente = round($porcentaje_envio->cliente/100,2);
			$porcentaje_empresa = round($porcentaje_envio->empresa/100,2);
			$cliente_envio = round($total_envio * $porcentaje_cliente,2);
			$empresa_envio = round($total_envio * $porcentaje_empresa,2);
		}

		$id_usuario = $this->session->id_usuario;
		$entrega=$this->session->entrega;
		$tipo = "";
		if($efectivo)
		{
			$tipo = "Efectivo";
		}
		if($transferencia)
		{
			$tipo = "Transferencia";
		}

		if($tarjeta)
		{
			$tipo = "Tarjeta";
		}

		$length = 6;
		$number = '0123456789';
		//$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
		date_default_timezone_set('America/El_Salvador');
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
		$form = array(
			'id_usuario' => $id_usuario,
			'total' => $total,
			'envio' => $total_envio,
			'envio_cliente' => $cliente_envio,
			'envio_empresa' => $empresa_envio,
			'fecha' => $fecha,
			'hora' => $hora,
			'tipo' => $tipo,
			'entrega'=>$entrega,
			'comprobante'=>$up_img1,
			'id_estado'=>1,
		);
		$form1 = array(
			'tipo' => "SALIDA",
			'total' => $total,
			'proceso' => "VENTA",
			'fecha' => $fecha,
			'hora' => $hora,
			'concepto'=>"DESCARGO DE PRODUCTO POR VENTA",
		);
		$table = "orden";
		$table1 = "movimiento_producto";
		$this->utils->begin();
		$insert = $this->utils->insert($table, $form);
		if($insert)
		{
			$id_orden = $this->utils->insert_id();
			$insert1 = $this->utils->insert($table1, $form1);
			if($insert1)
			{
				$id_movimiento = $this->utils->insert_id();
			}
			$caso = $randomString.$id_orden;
			$table_aux = "orden_detalle";
			$table_aux_b = "movimiento_producto_detalle";
			$error = 0;
			foreach ($this->cart->contents() as $items)
			{
				$idp = explode("_",$items["id"])[0];
				$form_aux = array(
					'id_orden' => $id_orden,
					'talla' => $items["talla"],
					'id_producto' => $idp,
					'cantidad' => $items["qty"],
					'precio' => $items["price"],
				);
				$insert_aux = $this->utils->insert($table_aux, $form_aux);
				if($this->productos->is_stockable($idp,$items["talla"])>0)
				{
					$exist = $this->productos->get_stock($idp,$items["talla"]);
					$exis = $exist->stock;
					$tot_mov += $items["qty"] * $items["price"];
					if($exis>0)
					{
						if($items["talla"]>0)
						{
							$form_aux_aux = array('cantidad' => ($exis-$items["qty"]));
							$form_aux_b = array('cantidad' => ($exis-$items["qty"]));
							$tablesa = "producto_talla";
							$where_b  = "id_talla='".$items["talla"]."'";
						}
						else {
							$form_aux_aux = array('stock' => ($exis-$items["qty"]));
							$form_aux_b = array('stock' => ($exis-$items["qty"]));
							$tablesa = "producto";
							$where_b  = "id_producto='".$idp."'";
						}
						$insert_auxb = $this->utils->update($tablesa, $form_aux_aux,$where_b);
						$form_auxb = array(
							'id_movimiento' => $id_movimiento,
							'id_producto' => $idp,
							'id_presentacion' => $items["talla"],
							'cantidad' => $items["qty"],
							'precio' => $items["price"],
						);
						$insert_aux_b = $this->utils->insert($table_aux_b, $form_auxb);
						if(!$insert_auxb)
						{
							$error=1;
						}
					}
				}
			}
			if(!$error)
			{
				$form_up = array('numero_orden' => $caso);
				$where  = "id_orden='".$id_orden."'";
				$update = $this->utils->update($table, $form_up, $where);
				if($update)
				{
					$this->cart->destroy();
					$this->utils->commit();
					$xdatos["typeinfo"] = "Success";
					$xdatos["msg"] = "Todo guardado con exito";
					$this->session->envio = 0;
					$this->session->norden = $caso;
					$this->session->total = $total;
					$this->session->fped = date("d-m-Y");
					$this->session->hped = date("h:i A");
				}
				else
				{
					$this->utils->rollback();
					$xdatos["typeinfo"] = "Error";
					$xdatos["msg"] = "No actualizo numero de orden".$this->utils->error();
				}
			}
			else {
				$this->utils->rollback();
				$xdatos["typeinfo"] = "Error";
				$xdatos["msg"] = "No guardaron los detalles de la orden".$this->utils->error();
			}
		}
		else {
			$this->utils->rollback();
			$xdatos["typeinfo"] = "Error";
			$xdatos["msg"] = "No guardo la orden".$this->utils->error();
		}
		echo json_encode($xdatos);
	}
	public function pago_orden(){
		define("UID", "bff2717bb2b635b67241befb9b8feadf");
		define("WSK", "9b54b194c5942533d8edb3f5805caf17");
		$Pagadito = new Pagadito(UID, WSK);
		$mensaje = "";
		$this->load->model("UtilsModel");
		$this->load->model("ProductoModel","productos");
		$total_envio = $this->session->envio;
		$total = $this->cart->total();
		$idcli = $this->session->id_usuario;
		$cliente = $this->utils->getIdMunCli($idcli);
		$id_mun = $cliente["id_municipio"];
		$porcentaje_envio = $this->utils->getPorcenaje($total,$id_mun);
		$costo_envio = $this->utils->getenvio();
		$total_envio = $costo_envio->costo_envio;
		$cargop = $costo_envio->cargo_porcentaje;
		$cargof = $costo_envio->cargo_fijo;
		$recargo = round($total*($cargop/100) + $cargof,2);
		if($porcentaje_envio != 0)
		{
			$porcentaje_cliente = round($porcentaje_envio->cliente/100,2);
			$porcentaje_empresa = round($porcentaje_envio->empresa/100,2);
			$cliente_envio = round($total_envio * $porcentaje_cliente,2);
			$empresa_envio = round($total_envio * $porcentaje_empresa,2);
		}
		$id_usuario = $this->session->id_usuario;
		$entrega=$this->session->entrega;
		$tipo = "Tarjeta";

		$length = 6;
		$number = '0123456789';
		//$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
		date_default_timezone_set('America/El_Salvador');
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
		$form = array(
			'id_usuario' => $id_usuario,
			'total' => $total,
			'cargo_tarjeta' => $recargo,
			'envio' => $total_envio,
			'envio_cliente' => $cliente_envio,
			'envio_empresa' => $empresa_envio,
			'fecha' => $fecha,
			'hora' => $hora,
			'tipo' => $tipo,
			'entrega'=>$entrega,
		);
		$form1 = array(
			'tipo' => "SALIDA",
			'total' => 0,
			'proceso' => "VENTA",
			'fecha' => $fecha,
			'hora' => $hora,
			'concepto'=>"DESCARGO DE PRODUCTO POR VENTA",
		);
		$table = "orden";
		$table1 = "movimiento_producto";
		$this->utils->begin();
		$insert = $this->utils->insert($table, $form);
		if($insert)
		{
			$id_orden = $this->utils->insert_id();
			$insert1 = $this->utils->insert($table1, $form1);
			if($insert1)
			{
				$id_movimiento = $this->utils->insert_id();
			}
			$caso = $randomString.$id_orden;
			$table_aux = "orden_detalle";
			$table_aux_b = "movimiento_producto_detalle";
			$error = 0;
			$tot_mov = 0;
			foreach ($this->cart->contents() as $items)
			{
				$idp = explode("_",$items["id"])[0];
				$form_aux = array(
					'id_orden' => $id_orden,
					'talla' => $items["talla"],
					'id_producto' => $idp,
					'cantidad' => $items["qty"],
					'precio' => $items["price"],
				);
				$insert_aux = $this->utils->insert($table_aux, $form_aux);

				if($this->productos->is_stockable($idp,$items["talla"])>0)
				{
					$exist = $this->productos->get_stock($idp,$items["talla"]);
					$exis = $exist->stock;
					$tot_mov += $items["qty"] * $items["price"];
					if($exis>0)
					{
						if($items["talla"]>0)
						{
							$form_aux_aux = array('cantidad' => ($exis-$items["qty"]));
							$form_aux_b = array('cantidad' => ($exis-$items["qty"]));
							$tablesa = "producto_talla";
							$where_b  = "id_talla='".$items["talla"]."'";
						}
						else {
							$form_aux_aux = array('stock' => ($exis-$items["qty"]));
							$form_aux_b = array('stock' => ($exis-$items["qty"]));
							$tablesa = "producto";
							$where_b  = "id_producto='".$idp."'";
						}
						$insert_auxb = $this->utils->update($tablesa, $form_aux_aux,$where_b);
						$form_auxb = array(
							'id_movimiento' => $id_movimiento,
							'id_producto' => $idp,
							'id_presentacion' => $items["talla"],
							'cantidad' => $items["qty"],
							'precio' => $items["price"],
						);
						$insert_aux_b = $this->utils->insert($table_aux_b, $form_auxb);
						if(!$insert_auxb)
						{
							$error=1;
						}
					}
				}
				if(!$insert_aux)
				{
					$error = 1;
				}
			}
			if(!$error)
			{
				if($tot_mov>0)
				{
					$form_up1 = array('total' => $tot_mov);
					$where1  = "id_movimiento='".$id_movimiento."'";
					$update1 = $this->utils->update($table1, $form_up1, $where1);
				}
				$form_up = array('numero_orden' => $caso);
				$where  = "id_orden='".$id_orden."'";
				$update = $this->utils->update($table, $form_up, $where);
				if($update)
				{
					$this->utils->commit();
					$this->session->envio = 0;
					$this->session->norden = $caso;
					$this->session->total = $total;
					$this->session->fped = date("d-m-Y");
					$this->session->hped = date("h:i A");
					if ($Pagadito->connect())
					{
						foreach ($this->cart->contents() as $items)
						{
							$Pagadito->add_detail($items["qty"], $items["name"], $items["price"], $items["image"]);
						}
						$Pagadito->add_detail(1, "ENVIO", $total_envio, "");
						$Pagadito->add_detail(1, "RECARGO", $recargo, "");
						$this->cart->destroy();

							//$Pagadito->set_custom_param("transaction", $orden);

							//Habilita la recepción de pagos preautorizados para la orden de cobro.
							$Pagadito->enable_pending_payments();

							$ern = $caso;
							if (!$Pagadito->exec_trans($ern)) {
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
							}
					}
					else {
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
					}
					$xdatos["typeinfo"] = "Success";
					$xdatos["msg"] = "Todo guardado con exito";
				}
				else
				{
					$this->utils->rollback();
					$xdatos["typeinfo"] = "Error";
					$xdatos["msg"] = "No actualizo numero de orden".$this->utils->error();
				}
			}
			else {
				$this->utils->rollback();
				$xdatos["typeinfo"] = "Error";
				$xdatos["msg"] = "No guardaron los detalles de la orden".$this->utils->error();
			}
		}
		else {
			$this->utils->rollback();
			$xdatos["typeinfo"] = "Error";
			$xdatos["msg"] = "No guardo la orden".$this->utils->error();
		}
		echo $mensaje;
	//	echo json_encode($xdatos);
	}
	public function finalizar_orden()
	{
		$data = array();

		$norden=$this->session->norden;
		$id_orden=$this->OrdenesModel->getIdOrden($norden);
		$orden= $this->OrdenesModel->getOrden($id_orden);
		$detalleOrden= $this->OrdenesModel->getOrdenDetRows($id_orden);

		foreach ($orden as $ord) {
			$correo=$ord['correo'];
		}
		//$correo=$orden[0]['correo'];
		$data['orden'] = $orden;
		$data['detOrden'] = $detalleOrden;

		$head='In5min- Confirmación de compra, ORDEN N°'.$norden;
		$body = $this->load->view('checkout/checkout_bill',$data,true);

		$this->load->library('email');
		$this->load->helper('email_helper');
		$this->email->set_mailtype('html');

		$this->email->from('info@tumundolaboral.com.sv', 'In5min');
		//$this->email->cc('info@opensolutionsystems.com', 'In5min');
		//$this->email->bcc('luis.aguilar@ues.edu.sv');
		$this->email->to($correo);
		$this->email->subject($head);
		$this->email->message($body);
		if ($this->email->send()) {

			$xdatos["type"] = "success";
			$xdatos["title"] = "Exito";
			$xdatos["message"] = "correo enviado ";
		} else {

			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["message"] = "Problema al enviar correo!";
		}
		echo json_encode($xdatos);
		$this->clean_session();
		//finalizar orden
		/*
		$this->session->unset_userdata("envio");
		$this->session->unset_userdata("entrega");
		$this->session->norden = "";
		$this->session->total = "";
		$this->session->fped = "";
		$this->session->hped = "";
		*/
	}
	function clean_session(){
		$this->session->unset_userdata("envio");
		$this->session->unset_userdata("entrega");
		$this->session->norden = "";
		$this->session->total = "";
		$this->session->fped = "";
		$this->session->hped = "";
	}
}
?>
