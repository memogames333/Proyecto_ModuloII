<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends yidas\rest\Controller {

	private $table = "cliente";

	function __construct()
	{
		parent::__construct();
		$this->load->model("api/ClientesModel", "clientes");
	}

	/**************************************************************/
	/*******************************API****************************/
	/**************************************************************/
	public function login(){
		$data = $this->request->getBodyParams();
		$result = $this->clientes->auth($data);
		if($result['success'])
		{
			unset($result['success']);
			$result['token'] = Authorization::generateToken($result['id']);
			unset($result['id']);
			$res = $this->pack($result, OK, "Login Successfully");
			return $this->response->json($res, OK);
		}

		$res = $this->pack($this->request->getBodyParams(), NOT_FOUND, "Login Fail");
		return $this->response->json($res, OK);
	}
	public function signup(){
		$data = $this->request->getBodyParams();
		$valid_fields = array('nombre','apellido','direccion','longitud','latitud','id_departamento','id_municipio','email','telefono','password');
		allowed_to_use($valid_fields,$data);
		$result = $this->clientes->save($data);

		if($result > 0)
		{
			return $this->response->json($this->pack($result, OK, "Register Successfully"));
		}
		else if($result == -1)
		{
			return $this->response->json($this->pack(null, SEE_OTHER, "Email all ready exist"));
		}
		return $this->response->json($this->pack($result, NO_CONTENT, "Register Fail"));
	}
	public function update()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$id_cliente = $result['data'];
		$valid_fields = array('nombre','apellido','direccion','longitud','latitud','id_departamento','id_municipio','email','telefono','password');
		allowed_to_use($valid_fields,$data);
		$result = $this->clientes->update($id_cliente,$data);

		if($result > 0){
			return $this->response->json($this->pack(null, OK, "Update Successfully"));
		}
		else if($result == -1)
		{
			return $this->response->json($this->pack(null, SEE_OTHER, "Email all ready exist"));
		}
		return $this->response->json($this->pack(null, NO_CONTENT, "Update Fail"));
	}

	public function updatePwd()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$id_cliente = $result['data'];
		if(isset($data["password_actual"]))
		{
			$passworda = $data["password_actual"];
			$coincide = $this->clientes->validarPassword($id_cliente,$passworda);
			if($coincide)
			{
				$valid_fields = array('password');
				allowed_to_use($valid_fields,$data);
				$result = $this->clientes->updatePwd($id_cliente,$data);

				if($result > 0)
				{
					$token = str_replace("Bearer ","",$this->input->get_request_header("Authorization"));
					$banned_arr = array("token" => $token);
					$banned = $this->clientes->blacklist($banned_arr);
					$responose["new_token"] = Authorization::generateToken($id_cliente);
					return $this->response->json($this->pack($responose, OK, "Update Successfully"));
				}
			}
			else {
				return $this->response->json($this->pack(null, UNAUTHORIZED, "Incorrect current password"));
			}
		}
		else {
			return $this->response->json($this->pack(null, NO_CONTENT, "Invalid current password"));
		}
		return $this->response->json($this->pack(null, NO_CONTENT, "Update Fail"));
	}

	public function verifyToken(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$token = str_replace("Bearer ","",$this->input->get_request_header("Authorization"));
		$black = $this->clientes->blacklistToken($token);
		if($black >0)
		{
			$res = $this->pack(null, FORBIDDEN, "Banned Token");
			return $this->response->json($res, OK);
		}
		$result = $this->clientes->get($result['data']);
		unset($result->id_user);
		unset($result->password);

		$id_departamento = $result->id_departamento;
		$id_municipio = $result->id_municipio;
		$depto = $this->clientes->getDepto($id_departamento);
		$mun = $this->clientes->getMun($id_municipio);
		if($depto != null)
		{
			$result->departamento = array('id' => $id_departamento, 'nombre' => $depto->nombre);
		}
		if($mun != null)
		{
			$result->municipio = array('id' => $id_municipio, 'nombre' => $mun->nombre);
		}
		unset($result->id_departamento);
		unset($result->id_municipio);

		parse($result,"usuarios");

		$res = $this->pack($result, OK, "Valid Token");
		return $this->response->json($res, OK);
	}
	public function collection(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$token = str_replace("Bearer ","",$this->input->get_request_header("Authorization"));
		$black = $this->clientes->blacklistToken($token);
		if($black >0)
		{
			$res = $this->pack(null, FORBIDDEN, "Banned Token");
			return $this->response->json($res, OK);
		}
		$result = $this->clientes->get($result['data']);
		unset($result->id_user);
		unset($result->password);

		$id_departamento = $result->id_departamento;
		$id_municipio = $result->id_municipio;
		$depto = $this->clientes->getDepto($id_departamento);
		$mun = $this->clientes->getMun($id_municipio);
		if($depto != null)
		{
			$result->departamento = array('id' => $id_departamento, 'nombre' => $depto->nombre);
		}
		if($mun != null)
		{
			$result->municipio = array('id' => $id_municipio, 'nombre' => $mun->nombre);
		}
		unset($result->id_departamento);
		unset($result->id_municipio);

		parse($result,"usuarios");
		$res = $this->pack($result, OK, "Datos de cliente");
		return $this->response->json($res, OK);
	}
	public function getsucursales(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$result = $this->clientes->get_sucursales();
		if($result != null)
		{
			parse($result,"sucursal");
		}
		$res = $this->pack($result, OK, "Sucursales");
		return $this->response->json($res, OK);
	}
	public function departamentos(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$result = $this->clientes->get_departamentos();
		if($result != null)
		{
			parse($result,"departamento");
		}
		$res = $this->pack($result, OK, "Departamentos");
		return $this->response->json($res, OK);
	}
	public function municipios($resourceID =-1){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		if (is_numeric($resourceID))
		{
			$result = $this->clientes->get_municipios($resourceID);
			if($result != null)
			{
				parse($result,"municipio");
			}
		}
		else {
			$result = $this->clientes->get_municipios();
			if($result != null)
			{
				parse($result, "municipio");
			}
		}
		$res = $this->pack($result, OK, "Municipios");
		return $this->response->json($res, OK);
	}
	public function orden($resourceID=-1)
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$id_cliente = $result['data'];
		if (is_numeric($resourceID))
		{
			$isyour = $this->clientes->isYourOrder($id_cliente, $resourceID);
			if($isyour)
			{
				$orden = $this->clientes->getByIdOrder($resourceID);
				if($orden != 0)
				{
					$orden_completa1 = array();
					foreach ($orden as $item)
					{
						$id_orden = $item["id_orden"];
						$detalles = $this->clientes->getByOrden($id_orden);
						if($detalles != 0)
						{
							$item["detalles"] = $detalles;
						}

						array_push($orden_completa1,$item);
					}


					return $this->response->json($this->pack($orden_completa1, OK, "Orden"));
				}
				else {
					return $this->response->json($this->pack(null, NOT_FOUND, "Ordenes"));
				}
			}
			else {
				return $this->response->json($this->pack("This order does not belong to you",FORBIDDEN));
			}
		}
		else
		{
			return $this->response->json($this->pack("Invalid argument",OK));
		}

	}
	public function wishlist_add()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$id_cliente = $result['data'];
		$data = $this->request->getBodyParams();
		/*foreach ($data as $list)
		{*/
		if(isset($data["id"]))
		{
			$isexist = $this->clientes->isExisItem($id_cliente, $data["id"]);
			if(!$isexist)
			{
				$datainsert = array('id_usuario' => $id_cliente, "id_producto" => $data["id"]);
				$insert = $this->clientes->addTowishlist($datainsert);
				if($insert)
				{
					return $this->response->json($this->pack(null, OK, "Product add Successfully"));
				}
				else {
					return $this->response->json($this->pack(null, BAD_REQUEST, "Product not added"));
				}
			}
			else {
				return $this->response->json($this->pack(null, BAD_REQUEST, "This product is already in your whishlist"));
			}
		}
		else {
			return $this->response->json($this->pack(null, BAD_REQUEST, "No id provided"));
		}
		//}
		/*if($nwl)
		{

		}
		return $this->response->json($this->pack(null, NOT_FOUND, "Ordenes"));
		*/
	}
	public function wishlist_remove()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$id_cliente = $result['data'];
		$data = $this->request->getBodyParams();

		if(isset($data["id"]))
		{
		/*foreach ($data as $list)
		{*/
			$isexist = $this->clientes->isExisItem($id_cliente, $data["id"]);
			if($isexist)
			{
				$datadel = array('id_usuario' => $id_cliente, "id_producto" => $data["id"]);
				$remove = $this->clientes->delFromwishlist($datadel);
				if($remove)
				{
					return $this->response->json($this->pack(null, OK, "Data Deleted Successfully"));
				}
				else {
					return $this->response->json($this->pack(null, BAD_REQUEST, "No data deleted"));
				}
			}
			else {
				return $this->response->json($this->pack(null, FORBIDDEN, "This product doesn't in your whishlist"));
			}
		}
		else {
			return $this->response->json($this->pack(null, BAD_REQUEST, "No id provided"));
		}
		//}
		/*if($nwl)
		{

		}
		return $this->response->json($this->pack(null, NOT_FOUND, "Ordenes"));
		*/
	}
	public function wishlist_get()
	{
		$result = Authorization::verifyToken();
		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$id_cliente = $result['data'];
		$data = array();
		//$data = $this->request->getBodyParams();
		$wish = $this->clientes->getWishList($id_cliente);
		if($wish != null)
		{
			$data = $wish;
		}
		return $this->response->json($this->pack($data, OK, $id_cliente));
	}
	public function getOrdenes()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}

		$data = array();
		$resourceID = $result['data'];
		if (is_numeric($resourceID))
		{
			$orden = $this->clientes->getByClient($resourceID);
			if($orden != 0)
			{
				$orden_completa = array();
				foreach ($orden as $item)
				{
					$id_orden = $item["id_orden"];
					$detalles = $this->clientes->getByOrden($id_orden);
					if($detalles != 0)
					{
						$item["detalles"] = $detalles;
					}
					array_push($orden_completa,$item);
				}
			}
			$data = $this->pack($orden_completa, OK, "Ordenes");
		}
		else
		{
			return $this->response->json($this->pack("Invalid argument",OK));
		}
		return $this->response->json($data, OK);

	}
	public function saveOrden()
	{

		$result = Authorization::verifyToken();

		if ($result['hasError']) {
			return $this->response->json($result['data'], $result['code']);
		}


		$this->db->trans_start();

		$body = $this->request->getBodyParams();
		$this->load->model("UtilsModel");

		$total = $body['total'];
		$porcentaje_envio = $this->UtilsModel->getPorcenaje($total);
		$costo_envio = $this->UtilsModel->getenvio();
		$total_envio = $costo_envio->costo_envio;
		if($porcentaje_envio != 0)
		{
			$porcentaje_cliente = round($porcentaje_envio->cliente/100,2);
			$porcentaje_empresa = round($porcentaje_envio->empresa/100,2);
			$cliente_envio = round($total_envio * $porcentaje_cliente,2);
			$empresa_envio = round($total_envio * $porcentaje_empresa,2);
		}

		$numero_orden = $this->get_code(8);

		$allowed_fields = array(
			"id_usuario",
			"total",
			"id_estado",
			"fecha",
			"hora",
			"numero_orden",
			"envio",
			"envio_empresa",
			"envio_cliente"
		);
		//$envios = $this->calculo_tarifa($body['id_empresa'],$body['id_direccion']);
		//$envio = $envios["envio"];
		//$envio_empresa = $envios["envio_empresa"];
		//$envio_driver = $envios["envio_driver"];
		$orden = array(
			"id_usuario" => $result['data'],
			"id_estado" => 1,
			//"id_direccion" => $body['id_direccion'],
			//"id_empresa" => $body['id_empresa'],
			"total" => $body['total'],
			"fecha" => date("Y-m-d"),
			"hora" => date("H:i:s"),
			"envio" => $total_envio,
			"envio_cliente" => $cliente_envio,
			"envio_empresa" => $empresa_envio
		);

		$allowed_fieldsd = array(
			"id_orden",
			"id_producto",
			"cantidad",
			"precio",
			"subtotal"
		);
		$ordenes = $this->clientes->saveGen("orden",$allowed_fields,$orden);
		$id_orden = $ordenes["id_orden"];
		$numero_orden = $numero_orden.$id_orden;
		foreach ($body["detalles"] as $key => $detalles)
		{
			$detalles['id_orden'] = $id_orden;
			$this->clientes->saveGen("orden_detalle",$allowed_fieldsd,$detalles);
		}
		$dataup = array('numero_orden' => $numero_orden);
		$this->clientes->updateGen("orden",$dataup,$id_orden);
		$this->db->trans_complete();
		$ordenes["numero_orden"] = $numero_orden;
		if( $ordenes != null)
		{
			parse($ordenes);
			$response = $this->pack($ordenes, CREATED, "Orden creada con exito");
		}else{
			$response = $this->pack(null, BAD_REQUEST, "Orden no creada");
		}

		return $this->response->json($response);
	}
	function get_code($n)
	{
		$length = $n;
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
		return $randomString;
	}
	/******************************************************/
	/******************************************************/
	/******************************************************/
	/******************************************************/
	/******************************************************/
	public function banner(){
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$result = $this->clientes->getBanner();
		if($result != null)
		{
			parse($result,"banner");
			url("url",$result);
		}
		$res = $this->pack($result, OK, "Banner Imgs");
		return $this->response->json($res, OK);
	}
	public function calificar()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$id_cliente = $result['data'];
		$id_orden = $data["id"];
		$cal = $data["calificacion"];
		$valid_fields = array('calificacion');
		allowed_to_use($valid_fields,$data);

		$result = $this->clientes->updateCal($id_orden,$data);
		$driver = $this->clientes->getDriverID($id_orden);
		$id_driver = $driver->id_driver;
		$ultimocal = $this->clientes->getDriverCal($id_driver);
		$caldb = $ultimocal->calificacion;
		$nuevo_cal = ($caldb + $cal) / 2;

		$datos = array('calificacion' => $nuevo_cal);
		$result2 = $this->clientes->updateCprom($id_driver,$datos);

		if($result > 0){
			return $this->response->json($this->pack(null, OK, "Update Successfully"));
		}

		return $this->response->json($this->pack(null, NO_CONTENT, "Update Fail"));
	}

	public function getTarifa()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$data = $this->request->getBodyParams();
		$total = $data['total'];
		if(true)//is_float($total) || is_numeric($total))
		{
			/*
			$id_empresa = $data["id_empresa"];
			$longitudi = floatval($data["longitud"]);
			$latitudi = floatval($data["latitud"]);

			$empresa = $this->clientes->getLocation($id_empresa);
			$longitudd = floatval($empresa->longitud);
			$latitudd = floatval($empresa->latitud);

			$rlat0 = deg2rad($latitudi);
			$rlng0 = deg2rad($longitudi);
			$rlat1 = deg2rad($latitudd);
			$rlng1 = deg2rad($longitudd);

			$latDelta = $rlat1 - $rlat0;
			$lonDelta = $rlng1 - $rlng0;
			$distance = (6371 *
			acos(
				cos($rlat0) * cos($rlat1) * cos($lonDelta) +
				sin($rlat0) * sin($rlat1)
				)
			);

			$distancia = round($distance,0);
			$datos_tarifa = $this->clientes->getTarifaC();
			$base = $datos_tarifa->base;
			$rango = $datos_tarifa->rango;
			$aumento = $datos_tarifa->aumento;

			if($distancia <= $rango)
			{
				$precio = $base;
			}
			else
			{
				$tramos = round((($distancia-$base)/$rango),0);
				$precio = $base+($aumento*$tramos);
			}
			*/
			//$result = $this->clientes->getTarifa($distancia);
			//$datas = array("distancia" => floatval($distancia), "tarifa"=> floatval($result->precio));
			//$datas = array("distancia" => floatval($distancia), "tarifa"=> floatval($precio));
			$this->load->model("UtilsModel");

			$minimo = $this->UtilsModel->getMinimo();

			$porcentaje_envio = $this->UtilsModel->getPorcenaje($total);
			$costo_envio = $this->UtilsModel->getenvio();
			$total_envio = $costo_envio->costo_envio;
			if($porcentaje_envio != 0)
			{
				$porcentaje_cliente = round($porcentaje_envio->cliente/100,2);
				$porcentaje_empresa = round($porcentaje_envio->empresa/100,2);
				$cliente_envio = round($total_envio * $porcentaje_cliente,2);
				$empresa_envio = round($total_envio * $porcentaje_empresa,2);
			}
			$datas = array("total"=> floatval($total),"tarifa"=> floatval($cliente_envio), 'minimo' => floatval($minimo->minimo));
			return $this->response->json($this->pack($datas, OK, "Tarifa"));
		}
		else {
			return $this->response->json($this->pack(null, NOT_CONTENT, "Invalid argument"));
		}
	}
	/**************************************************************/
	/*******************************API****************************/
	/**************************************************************/
}
