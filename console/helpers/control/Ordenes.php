<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ordenes extends CI_Controller {

	private $table = "orden";

	public function __construct()
	{
		parent::__construct();
		$this->load->model("OrdenesModel","ordenes");
		$this->load->model("PerfilModel","perfil");
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
		validate_profile($this);
		//Do your magic here
	}

	public function index()
	{

		$id_usuario = $this->session->id_usuario;
		$row = $this->perfil->user_info($id_usuario);
		$datos = array(
			"row"=>$row,
			"url"=>"ordenes",
		);
		$dash = $this->load->view('perfil/dash_perfil', $datos, TRUE);
		$data = array(
			"id_usuario"=>$id_usuario,
			"dash"=>$dash,
		);
		$extras = array(
			'css' => array(
				0 => "libs/datatables/datatables.css",
			),
			'js' => array(
				0 => "js/funciones/ordenes.js",
				1 => "libs/datatables/datatables.js",
			),
		);
		template("perfil/ordenes",$data,$extras);
	}

	function get_data(){
		$id_usuario = $this->session->id_usuario;
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		$fecha_filtro = intval($this->input->post("fecha_filtro"));

		$order = $this->input->post("order");
		$search = $this->input->post("search");
		$search = $search['value'];
		$col = 0;
		$dir = "";
		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$dir = $o['dir'];
			}
		}

		if ($dir != "asc" && $dir != "desc") {
			$dir = "desc";
		}
		$valid_columns = array(
			0 => 'numero_orden',
			1 => 'fecha',
			3 => 'total',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->ordenes->get_collection($order, $search, $valid_columns, $length, $start, $dir,$id_usuario,$fecha_filtro);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {
				$menudrop = '<div class="btn-group">
                 	<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Menu <i class="fas fa-list-alt"></i></button>
					<div class="dropdown-menu">';
				$menudrop .= "<a href='".base_url("orden/".$rows->numero_orden)."' class='dropdown-item delete_row' style='color:#1b55ae;' id='$rows->id_orden' ><i class='fas fa-eye'></i> Ver Orden</a>";

				if ($rows->finalizada==1) $estado = "<span class='badge badge-primary'>Finalizada</span>";
				else if($rows->anulada==1) $estado = "<span class='badge badge-warning'>Anulada</span>";
				else if($rows->cancelada==1) $estado = "<span class='badge badge-danger'>Cancelada</span>";
				else{
					//$menudrop .= "<a class='dropdown-item delete_row text-danger' id='$rows->id_orden' ><i class='fas fa-trash'></i> Cancelar</a>";
					$estado = "<span class='badge badge-success'>En Proceso</span>";
				}

				$menudrop .= "</div></div>";

				$orden = "<p style='font-weight: bold;color:#012770 '>$rows->numero_orden</p>";

				$data[] = array(
					$orden,
					d_m_Y($rows->fecha),
					$estado,
					"$".number_format($rows->total,2,".",","),
					$menudrop,
				);
			}
			$total = $this->ordenes->total_rows($id_usuario);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $total,
				"recordsFiltered" => $total,
				"data" => $data
			);
		} else {
			$data[] = array(
				"",
				"",
				"No se encontraron registros",
				"",
				"",
			);
			$output = array(
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => $data
			);
		}
		echo json_encode($output);
		exit();
	}

	function orden($orden = ""){
		if (!isset($this->session->logged_in)){
			redirect("home","refresh");
		}else{
			$id_usuario = $this->session->id_usuario;
			$n_orden = $this->uri->segment(2);
			$datos = array(
				"row"=>$this->perfil->user_info($id_usuario),
				"url"=>"ordenes",
			);
			$dash = $this->load->view('perfil/dash_perfil', $datos, TRUE);

			$row = $this->ordenes->get_row_info($n_orden,$id_usuario);
			$id_orden = $row->id_orden;
			$count = $this->ordenes->count_products($id_orden);
			$products = $this->ordenes->get_products_list($id_orden);
			if($row!=null){
				$data = array(
					"id_usuario"=>$id_usuario,
					"dash"=>$dash,
					"n_orden"=>$n_orden,
					"row"=>$row,
					"products"=>$products,
					"count"=>$count,
				);
				$extras = array(
					'css' => array(
						0 => "libs/datatables/datatables.css",
					),
					'js' => array(
						0 => "js/funciones/ordenes.js",
						1 => "libs/datatables/datatables.js",
					),
				);
				template("perfil/ver_orden",$data,$extras);
			}
			else{
				redirect("home","refresh");
			}
		}
	}

}

/* End of file Perfil.php */
