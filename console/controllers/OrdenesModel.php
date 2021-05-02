<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrdenesModel extends CI_Model
{
	private $table = "ordenes";
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
		$this->db->limit($length, $start);
		$clients = $this->db->get("usuarios");
		if ($clients->num_rows() > 0) {
			return $clients->result();
		} else {
			return 0;
		}
	}
	function totalclients(){
		$clients = $this->db->get("clients");
		if ($clients->num_rows() > 0) {
			return $clients->num_rows();
		} else {
			return 0;
		}
	}

	function exits_car($name,$address,$cellphone){
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

	function get_client_info($id_client){
		$this->db->where('id_client', $id_client);
		$clients = $this->db->get("clients");
		if ($clients->num_rows() > 0) {
			return $clients->row();
		} else {
			return 0;
		}
	}

	function get_state($id_client){
		$this->db->where('active', 1);
		$this->db->where('id_client', $id_client);
		$clients = $this->db->get("clients");
		if ($clients->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function getIdOrden($norden){
		$this->db->where('numero_orden', $norden);
		$query = $this->db->get("orden");
		if($query->num_rows()>0)     
			$result = $query->row()->id_orden;     
		else
			$result=0;
		return $result;
			
	}
		function getOrden($norden){
		$SqlInfo="o.numero_orden,o.total,o.envio,o.fecha,o.hora,o.tipo, u.nombre, u.direccion, u.telefono ";
		$this->db->select($SqlInfo);	
		 $this->db->from('orden AS o, usuarios AS u ');
		$this->db->where('o.id_usuario', 'u.id_usuario');
		$this->db->where('o.id_orden', $norden);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
			
	}
		function getOrdenRows($norden){
		$this->db->where('id_orden', $norden);
		$records = $this->db->get("orden_detalle");
		$ordenDet = $records->result_array();
		return $ordenDet;
	}
	
		function getOrdenDetRows($id_orden){
		$SqlInfo="od.id_producto,od.cantidad,od.precio,p.descripcion ";	
		$this->db->select($SqlInfo);	
		 $this->db->from('orden_detalle AS od, producto AS p ');
		$this->db->where('od.id_producto', 'p.id_producto');
		$this->db->where('od.id_orden', $id_orden);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

}

/* End of file OrdenesModel.php */
