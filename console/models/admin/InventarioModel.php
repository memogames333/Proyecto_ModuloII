<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InventarioModel extends CI_Model
{
	private $table = "movimiento_producto";

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
		$this->db->where("inactivo", "0");
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
				$descripcion1 = $row['descripcion'];
				$precio = $row['precio'];
				$stock = round($row['stock'],0);
			}

			$lista = "";
			$this->db->where("id_producto", $id_producto);
			$sql = $this->db->get("producto_tipo");
			if($sql->num_rows() > 0)
			{
				foreach ($sql->result_array() as $key)
				{
					$id = $key["id"];
					$id_tipo = $key["id_tipo"];
					$descripcion = $key["descripcion"];
					$color = $key["color"];
					$lista .="<tr class='celda".$id_producto."'>";
					$lista .="<td><input type='hidden' id='bandera' name='bandera' value='secundario'><input type='hidden' class='producto_id' id='producto_id' value='".$id_producto."'></td>";
					$lista .="<td colspan='4'>";
					$lista .="<input type='hidden' id='id_detp' name='id_detp' value='".$id."'>";
					$lista .="<input type='hidden' id='id_tipo' name='id_tipo' value='".$id_tipo."'>";
					$lista .="<input type='hidden' id='color' name='color' value='".$color."'>";
					$lista .="<select class='form-control selector_color sel' style='width:50%;'>";
					$lista .="<option value=''>Seleccione ".$descripcion."</option>";

					$this->db->where("id_pt", $id);
					$sql1 = $this->db->get("producto_tipo_detalle");
					if($sql1->num_rows() > 0)
					{
						foreach ($sql1->result_array() as $key)
						{
							$id_detalle = $key["id_detalle"];
							$descripcion = $key["descripcion"];
							$aumento = $key["aumento"];
							$nombre_color = $key["nombre_color"];
							if($color == 1)
							{
								$col = explode(": ", $descripcion);
								$col1 = $col[1];

								$lista .="<option value='".$id_detalle."' data-color='".$col1."'>".$nombre_color."</option>";
							}
							else
							{
								$lista .="<option value='".$id_detalle."'>".$descripcion."</option>";
							}
						}
					}
					$lista .="</select>";
					$lista .="</td>";
					$lista .="</tr>";
				}
			}
			// $precios = $this->get_tallas($id_producto);

			// if($precios != null)
			// {
			// 	$lista .= "<select class='form-control tallas sel' style='width:60px;'>";
			// 	$fr = 0;
			// 	foreach ($precios as $row_por)
			// 	{
			// 		if(!$fr)
			// 		{
			// 			$stock = $row_por->cantidad;
			// 			$fr = 1;
			// 		}
			// 		$lista .= "<option value='".$row_por->id_talla."' stock='".$row_por->cantidad."'>".$row_por->talla."</option>";
			// 	}
			// 	$lista .= "</select>";
			// }
			$xdatos['descripcion'] =$descripcion1;
      $xdatos['precios'] = $precio;
      $xdatos['stock'] = $stock;
      $xdatos['lista'] = $lista;
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

	public function get_tallas($id){
		$this->db->where('id_producto', $id);
		$row = $this->db->get("producto_talla");
		if ($row->num_rows() > 0) {
			return $row->result();
		} else {
			return null;
		}
	}
	public function get_stock_talla($id){
		$this->db->where('id_talla', $id);
		$row = $this->db->get("producto_talla");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return null;
		}
	}

	function get_movimiento($id){
		$this->db->where('id_movimiento', $id);
		$row = $this->db->get("movimiento_producto");
		if ($row->num_rows() > 0) {
			return $row->row();
		} else {
			return 0;
		}
	}
	function detalles($id_mov){
		// $sql = "SELECT * FROM producto";
		// $this->db->select("p.id_producto AS ID, p.descripcion AS Descripcion, p.precio AS Precio, p.stock AS Stock_actual, '' AS Cantidad");
		$this->db->where("id_movimiento", $id_mov);
		$this->db->select('p.descripcion, md.cantidad, md.id_presentacion, md.precio');
		$this->db->join('producto AS p', 'p.id_producto = md.id_producto', 'left');
		$query = $this->db->get("movimiento_producto_detalle AS md");
		// Atentos a esta función que transforma el resultado de una query en CSV
		// foreach($query->result_array() as $row)
		// {
		// 	$output[] = array('producto' => $row["id_producto"]."| ".$row["descripcion"]);
		// 	$descripcion = $row['descripcion'];
		// 	$precio = $row['precio'];
		// 	$stock = $row['stock'];
		// }
		return $query->result_array();
	}

	function verificar($id_producto, $detalle)
	{
		$this->db->where("id_producto", $id_producto);
		$this->db->where("caracteristicas", $detalle);
		$query = $this->db->get("stock");
		return $query->num_rows();
	}


}

/* End of file ClientModel.php */
