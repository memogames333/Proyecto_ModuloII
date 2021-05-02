<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class driversModel extends CI_Model
{
	var $table = "driver";
	var $mail = "correo";
	var $pk = "id_driver";

	function get_collection($order, $search, $valid_columns, $length, $start, $dir)
	{
		if ($order !=	 null) {
			$this->db->order_by($order, $dir);
		}
		if (!empty($search)) {
			$x = 0;
			foreach ($valid_columns as $sterm) {
				if ($x == 0) {
					$this->db->like($sterm, $search);
				} else {
					$this->db->or_like($sterm, $search);
				}
				$x++;
			}
		}
		$this->db->select('*');
		$this->db->limit($length, $start);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	function total_rows(){
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->num_rows();
		} else {
			return 0;
		}
	}

	function exits_row($name,$address,$cellphone){
		$this->db->where('name', $name);
		$this->db->where('address', $address);
		$this->db->where('cellphone', $cellphone);
		$clients = $this->db->get("clients");
		if ($clients->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_row_info($id){
		$this->db->where('id_driver', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
		}
	}

	function get_state($id){
		$this->db->where('estado', 1);
		$this->db->where('id_driver', $id);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	function get_all(){
		$this->db->where('estado', 1);
		$clients = $this->db->get($this->table);
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	function get_categorias(){
		$clients = $this->db->get("categoria");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}

	/**************************************************************/
	/*******************************API****************************/
	/**************************************************************/
	public function isYourOrder($id,$order){
		$this->db->select();
		$this->db->where("id_driver",$id);
		$this->db->where("id_paquete",$order);
		$result = $this->db->get("cola_orden_paquete");
		if($result->num_rows()>0)
		{
			return 1;
		}
		else {
			return 0;
		}
	}
	public function getClientToken($id)
	{
		return $this->db->select("token")
					->where("id_cliente",$id)
					->get("cliente_token")
					->result();
	}
	public function get($userId = -1){

		$this->db->select("nombre, telefono, foto,tipo_vehiculo as vehiculo, placa, tiempo, calificacion, correo as email");

		if($userId > -1){
			$this->db->where($this->pk, $userId);
			return $this->db->get($this->table, 1)->row();
		}

		return $this->db->get($this->table)->result();

	}

	public function getByDriver($driverID){
		$this->db->select("orden.id_orden,orden.fecha, orden.hora,orden.total,orden.envio,orden.numero_orden,orden.id_direccion,orden.id_empresa,'' as detalles,'' as direccion, '' as empresa");
		$this->db->select("cliente.nombre as cliente");
		//$this->db->select("empresa.nombre as empresa");
		$this->db->select("estado.descripcion as estado");
		$this->db->from("cola_orden_paquete");
		$this->db->join("orden", "cola_orden_paquete.id_paquete=orden.id_orden");
		$this->db->join("cliente","orden.id_cliente=cliente.id_cliente");
		//$this->db->join("empresa","orden.id_empresa=empresa.id_empresa");
		$this->db->join("estado","orden.id_estado=estado.id_estado");
		$this->db->where("cola_orden_paquete.id_driver", $driverID);
		$this->db->order_by("cola_orden_paquete.id_paquete", "ASC");
		$result = $this->db->get()->result();

		parse($result,"orden");
		return $result;
	}
	public function getGanancia($id,$desde,$hasta){
		$this->db->select("orden.id_orden,orden.fecha, orden.hora,orden.total,orden.envio,orden.envio_driver, envio_empresa,orden.numero_orden");
		$this->db->from("orden");
		$this->db->join("cola_orden_paquete", "cola_orden_paquete.id_paquete=orden.id_orden");
		$this->db->where("orden.id_estado", 5);
		$this->db->where("cola_orden_paquete.id_driver", $id);
		$this->db->where("cola_orden_paquete.fecha BETWEEN '$desde' AND '$hasta'");
		$result = $this->db->get()->result();
		parse($result,"orden");
		return $result;
	}
	public function getGananciaTotal($id,$desde,$hasta){
		$this->db->select("SUM(envio_driver) as envio_driver, SUM(envio_empresa) as envio_empresa");
		$this->db->from("orden");
		$this->db->join("cola_orden_paquete", "cola_orden_paquete.id_paquete=orden.id_orden");
		$this->db->where("orden.id_estado", 5);
		$this->db->where("cola_orden_paquete.id_driver", $id);
		$this->db->where("cola_orden_paquete.fecha BETWEEN '$desde' AND '$hasta'");
		$result = $this->db->get()->row();
		parse($result,"orden");
		return $result;
	}
	public function getInfoTime($id){
		$this->db->select("fecha, hora");
		$this->db->where("id_orden", $id);
		$this->db->where("id_estado", 3);
		$result = $this->db->get("cola_seguimiento")->row();
		return $result;
	}
	public function getLiquidacion($id,$desde,$hasta){
		$this->db->select("orden.id_orden,orden.fecha, orden.hora,orden.total, orden.envio,orden.numero_orden");
		$this->db->from("orden");
		$this->db->join("cola_orden_paquete", "cola_orden_paquete.id_paquete=orden.id_orden");
		$this->db->where("orden.id_estado", 5);
		$this->db->where("cola_orden_paquete.id_driver", $id);
		$this->db->where("cola_orden_paquete.fecha BETWEEN '$desde' AND '$hasta'");
		$result = $this->db->get()->result();
		parse($result,"orden");
		return $result;
	}
	public function getLiquidacionTotal($id,$desde,$hasta){
		$this->db->select("SUM(total+envio) as total");
		$this->db->from("orden");
		$this->db->join("cola_orden_paquete", "cola_orden_paquete.id_paquete=orden.id_orden");
		$this->db->where("orden.id_estado", 5);
		$this->db->where("cola_orden_paquete.id_driver", $id);
		$this->db->where("cola_orden_paquete.fecha BETWEEN '$desde' AND '$hasta'");
		$result = $this->db->get()->row();
		parse($result,"orden");
		return $result;
	}
	public function vigentToken($token)
	{
		return  $this->db->select()
		->where("access_token", $token)
		->get("driver")
		->num_rows();
	}
	public function getByIdOrder($id){
		$this->db->select("orden.id_orden,orden.fecha, orden.hora,orden.total,orden.envio,orden.numero_orden,orden.id_direccion,orden.id_empresa,'' as detalles,'' as direccion,'' as empresa");
		$this->db->select("cliente.nombre as cliente");
		//$this->db->select("empresa.nombre as empresa");
		$this->db->select("estado.descripcion as estado");
		$this->db->from("cola_orden_paquete");
		$this->db->join("orden", "cola_orden_paquete.id_paquete=orden.id_orden");
		$this->db->join("cliente","orden.id_cliente=cliente.id_cliente");
		//$this->db->join("empresa","orden.id_empresa=empresa.id_empresa");
		$this->db->join("estado","orden.id_estado=estado.id_estado");
		$this->db->where("cola_orden_paquete.id_paquete", $id);
		$result = $this->db->get()->result();

		parse($result,"orden");
		return $result;
	}
	public function getDireccion($id_direccion){
		$this->db->select("cliente_direccion.direccion,cliente_direccion.referencia,cliente_direccion.longitud, cliente_direccion.latitud");
		$this->db->select("departamento.nombre as departamento");
		$this->db->select("municipio.nombre as municipio");
		$this->db->from("cliente_direccion");
		$this->db->join("departamento", "cliente_direccion.id_departamento=departamento.id_departamento");
		$this->db->join("municipio", "cliente_direccion.id_municipio=municipio.id_municipio");
		$this->db->where("cliente_direccion.id_direccion", $id_direccion);
		$result = $this->db->get();
		if($result->num_rows()>0)
		{
			$resulta = $result->row();
			parse($resulta, "cliente_direccion");
			return $resulta;
		}
		return 0;
	}
	public function getEmpresa($id_empresa){
		$this->db->select("nombre, direccion, telefono, longitud, latitud");
		$this->db->where("id_empresa", $id_empresa);
		$result = $this->db->get("empresa")->row();
		parse($result, "empresa");
		return $result;
	}
	public function getIdClient($id){
		$this->db->select("id_cliente");
		$this->db->where("id_orden", $id);
		$result = $this->db->get("orden")->row();
		return $result;
	}
	public function getById($id_driver){

		$loginResult = array(
			"success" => FALSE
		);

		$result = $this->db->select()
			->where($this->pk, $id_driver)
			->get($this->table)
			->row();

		if($result !== null){
				$loginResult['nombre'] = $result->nombre;
				$loginResult['telefono'] = $result->telefono;
				$loginResult['foto'] = base_url().$result->foto;
				$loginResult['vehiculo'] = $result->tipo_vehiculo;
				$loginResult['placa'] = $result->placa;
				$loginResult['tiempo'] = $result->tiempo;
				$loginResult['calificacion'] = $result->calificacion;
				$loginResult['email'] = $result->correo;
				$loginResult['success'] = TRUE;
				parse($loginResult,$this->table);
				return $loginResult;
		}

		return $loginResult;
	}
	public function get_estados(){
		$result = $this->db->select()->get("estado")->result();
		parse($result,"estado");
		return $result;
	}
	public function getDriverTime($id){
		$result = $this->db->select("tiempo")->where('id_driver',$id)->get("driver")->row();
		return $result;
	}
	public function auth($data){

		$loginResult = array(
			"success" => FALSE
		);

		$result = $this->db->select()
			->where($this->mail, $data['email'])
			->get($this->table, 1)
			->row();

		if($result !== null){

			$password = decrypt($result->password);
			if($password == $data['password']){
				if(isset($data["token"]))
				{
					$this->updateToken($result->id_driver,array('token'=>$data["token"]));
				}
				$loginResult['user_id'] = $result->id_driver;
				$loginResult['nombre'] = $result->nombre;
				$loginResult['telefono'] = $result->telefono;
				$loginResult['foto'] = base_url().$result->foto;
				$loginResult['vehiculo'] = $result->tipo_vehiculo;
				$loginResult['placa'] = $result->placa;
				$loginResult['tiempo'] = $result->tiempo;
				$loginResult['calificacion'] = $result->calificacion;
				$loginResult['email'] = $result->correo;
				$loginResult['success'] = TRUE;
				parse($loginResult,$this->table);
				return $loginResult;
			}

		}

		return $loginResult;
	}
	public function save_seguimiento($data)
	{
			$this->db->insert("cola_seguimiento", $data);
			return $this->db->insert_id();

	}
	public function update($id,$data)
	{
			$result = $this->db->update("orden", $data, array("id_orden" => $id));
			return $result;
	}
	public function updateCola($id,$data)
	{
			$result = $this->db->update("cola_orden_paquete", $data, array("id_paquete" => $id));
			return $result;
	}
	public function updateTprom($id,$data)
	{
			$result = $this->db->update("driver", $data, array("id_driver" => $id));
			return $result;
	}
	public function updateToken($id,$data)
	{
			$result = $this->db->update($this->table, $data, array("id_driver" => $id));
			return $result;
	}
	public function setToken($id,$data)
	{
			$result = $this->db->update($this->table, $data, array("id_driver" => $id));
			return $result;
	}
	/**************************************************************/
	/*******************************API****************************/
	/**************************************************************/
}

/* End of file ClientModel.php */
