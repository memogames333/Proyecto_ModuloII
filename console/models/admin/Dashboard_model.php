<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{

	function grafica_productos_vendidos(){
		$this->db->select('COUNT("p.id_producto") as total,p.descripcion as producto');
		$this->db->where("o.finalizada",1);
		$this->db->join('orden_detalle as od', 'od.id_producto = p.id_producto');
		$this->db->join('orden as o', 'od.id_orden = o.id_orden');
		$this->db->group_by('p.id_producto');
		$this->db->order_by('total', 'desc');
		$this->db->limit(6);
		$query=$this->db->get('producto as p');
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return 0;
		}
	}

	//Devuelve el dato de vacaciones en un rango de fechas
	function grafica_ingreso_mes($actual,$anterior){
		$this->db->select('SUM(total) as total');
		$this->db->where('finalizada',1);
		$this->db->where("fecha BETWEEN '$actual' AND '$anterior'");
		$query=$this->db->get('orden');
		if($query->num_rows()>0){
			return $query->row();
		}else{
			return 0;
		}
	}

	//Devuelve el dato de vacaciones en un rango de fechas
	function grafica_ordenes_finalizada($actual,$anterior){
		$this->db->select('COUNT(id_orden) as total');
		$this->db->where('finalizada',1);
		$this->db->where("fecha BETWEEN '$actual' AND '$anterior'");
		$row=$this->db->get('orden');
		if($row->num_rows()>0){
			return $row->row();
		}else{
			return 0;
		}
	}
	function grafica_ordenes_anulada($actual,$anterior){
		$this->db->select('COUNT(id_orden) as total');
		$this->db->where('anulada',1);
		$this->db->where("fecha BETWEEN '$actual' AND '$anterior'");
		$row=$this->db->get('orden');
		if($row->num_rows()>0){
			return $row->row();
		}else{
			return 0;
		}
	}
	function grafica_ordenes_cancelada($actual,$anterior){
		$this->db->select('COUNT(id_orden) as total');
		$this->db->where('cancelada',1);
		$this->db->where("fecha BETWEEN '$actual' AND '$anterior'");
		$row=$this->db->get('orden');
		if($row->num_rows()>0){
			return $row->row();
		}else{
			return 0;
		}
	}


}

/* End of file Dashboard_model.php */
