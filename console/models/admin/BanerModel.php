<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BanerModel extends CI_Model
{
	private $table = "baner";

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
		// $this->db->join('', '');
		$this->db->limit($length, $start);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}
	function total_rows(){
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return $row->num_rows();
		} else {
			return 0;
		}
	}

	function exits_row($name,$address,$cellphone){
		$this->db->where('name', $name);
		$this->db->where('address', $address);
		$this->db->where('cellphone', $cellphone);
		$row = $this->db->get("clients");
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_row_info($id){
		$this->db->where('id_producto', $id);
		$row = $this->db->get("producto");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}

	function get_state($id){
		$this->db->where('inactivo', 1);
		$this->db->where('id_producto', $id);
		$row = $this->db->get($this->table);
		if ($row->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	function get_categorias(){
		$row = $this->db->get("categoria");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return 0;
		}
	}

	function traer_cliente($query)
	{
		$this->db->where("tipo_producto", "FISICO");
		$query=urldecode($query);
		$this->db->like('descripcion', $query);
		//  $this->db->limit(100);
		$query = $this->db->get('producto');
		if($query->num_rows() > 0)
		{
			$output = array();
			foreach($query->result_array() as $row)
			{
				$output[] = array('producto' => $row["id_producto"]."| ".$row["descripcion"]);
			}
			echo json_encode($output);
		}
	}

	function consultar_prod($id_producto)
	{
		$id_producto=urldecode($id_producto);
		$this->db->where("id_producto", $id_producto);
		// $this->db->like('descripcion', $query);
		//  $this->db->limit(100);
		$query = $this->db->get('producto');
		if($query->num_rows() > 0)
		{
			// $output = array();
			// $row = $query->result_array();
			foreach($query->result_array() as $row)
			{
				// $output[] = array('producto' => $row["id_producto"]."| ".$row["descripcion"]);
				$descripcion = $row['descripcion'];
				$precio = $row['precio'];
				$stock = $row['stock'];
			}

			$xdatos['descripcion'] =$descripcion;
      $xdatos['precios'] = $precio;
      $xdatos['stock'] = $stock;
			echo json_encode($xdatos);
			// print_r($row);
		}
	}

	function lista(){
		// $sql = "SELECT * FROM producto";
		$this->db->where('tipo_producto', "FISICO");
		$this->db->select("p.id_producto AS ID, p.descripcion AS Descripcion, p.precio AS Precio, p.stock AS Stock_actual, '' AS Cantidad");
		$query = $this->db->get("producto AS p");
		// Atentos a esta función que transforma el resultado de una query en CSV
		return $this->dbutil->csv_from_result($query);
	}

	function get_baner($id){
		$this->db->where('id_baner', $id);
		$row = $this->db->get("baner");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}

  function get_num(){
		// $this->db->where('id_baner', $id);
		$row = $this->db->get("baner");
	  return $row->num_rows();
	}

	function detalles(){
		// $sql = "SELECT * FROM producto";
		// $this->db->select("p.id_producto AS ID, p.descripcion AS Descripcion, p.precio AS Precio, p.stock AS Stock_actual, '' AS Cantidad");
		// $this->db->where("id_movimiento", $id_mov);
		// $this->db->select('p.descripcion, md.cantidad, md.precio');
		// $this->db->join('producto AS p', 'p.id_producto = md.id_producto', 'left');
		$query = $this->db->get("baner");
		return $query->result_array();
	}

}

/* End of file ClientModel.php */
