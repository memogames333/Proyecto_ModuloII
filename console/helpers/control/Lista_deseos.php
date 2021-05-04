<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lista_deseos extends CI_Controller {

	private $table = "wishlist";

	public function __construct()
	{
		parent::__construct();
		$this->load->model("ListaDeseosModel","lista");
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
			"url"=>"lista_deseos",
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
				0 => "js/funciones/wishlist.js",
				1 => "libs/datatables/datatables.js",
			),
		);
		template("perfil/wishlist",$data,$extras);
	}

	function get_data(){

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
			1 => 'producto.id_producto',
			3 => 'producto.descripcion',
			4 => 'producto.precio',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}
		$id_usuario = $this->session->id_usuario;
		$row = $this->lista->get_collection($order, $search, $valid_columns, $length, $start, $dir,$id_usuario,$fecha_filtro);

		if ($row != 0) {
			$data = array();
			$n = 1;
			foreach ($row as $rows) {
				$rutaImgProd=base_url("assets/".$rows->imagen);
				if ($rows->imagen=="") $rutaImgProd=base_url("assets/img/productos/no_disponible.png");
				$menudrop = '<div class="btn-group">
                 	<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Menu <i class="fas fa-list-alt"></i></button>
					<div class="dropdown-menu">';
				$menudrop .= "<button type='button' class='dropdown-item add_cart' style='color:#03a9f5;'  id='$rows->id_wishlist' data-nombre='$rows->descripcion' data-imagen='$rutaImgProd' data-precio='$rows->precio'>";
				$menudrop .= "<i class='fas fa-shopping-cart'></i> Agregar a Carrito</button>";
				$menudrop .= "<a href='".base_url('producto/'.md5($rows->id_producto)). "' class='dropdown-item' style='color:#43a009;' id='$rows->id_wishlist' ><i class='fas fa-eye'></i> Ver Producto</a>";
				$menudrop .= "<button type='button' class='dropdown-item delete_row' style='color:#f01817;' id='$rows->id_wishlist' ><i class='fas fa-trash'></i> Eliminar</button>";

				$menudrop .= "</div></div>";

				$data[] = array(
					$n,
					$rows->descripcion,
					$rows->marca,
					"$".number_format($rows->precio,2,".",","),
					$menudrop,
				);
				$n++;
			}
			$total = $this->lista->total_rows($id_usuario);
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

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_wishlist ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->delete($this->table,$where);
			if($delete) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Producto eliminado con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Producto no pudo ser eliminado!";
			}
			echo json_encode($data);
		}
	}

}

/* End of file Perfil.php */
