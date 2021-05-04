<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos_destacados extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "productos_destacados";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/ProductosDestacadosModel","productos");
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Productos Destacados",
			"icono"=> "fa fa-bar-chart",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-plus",
					'url' => 'productos_destacados/agregar',
					'txt' => 'Agregar Producto Destacado',
					'modal' => true,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Descripcion"=>2,
				"Marca"=>2,
				"Categoria	"=>2,
				"Estado"=>1,
				"Acciones"=>1,
			),
			"urljs"=>"funciones_productos_destacados.js",
		);
		$extras = array(
			'css' => array(),
			'js' => array(
				0 => 'admin/libs/autocomplete/jquery.autocomplete.min.js'
			),
		);
		layout("admin/template/admin",$data,$extras);
	}

	function get_data(){

		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));

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
			0 => 'id_producto_destacado',
			1 => 'p.descripcion',
			2 => 'p.marca',
			3 => 'categoria',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->productos->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			$n=1;
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";

				if($rows->activo==1){
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-primary font-bold'>Activo<span>";
					$icon = "fa fa-toggle-off";
				}
				else{
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "fa fa-toggle-on";
				}
				$menudrop .= "<li><a class='state_change' data-state='$txt'  id=" . $rows->id_producto_destacado . " ><i class='$icon'></i> $txt</a></li>";

				$menudrop .= "<li><a class='delete_row'  id=" . $rows->id_producto_destacado . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";


				$data[] = array(
					$n++,
					$rows->descripcion,
					$rows->marca,
					$rows->categoria,
					$show_text,
					$menudrop,
				);
			}
			$total = $this->productos->total_rows();
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

	function agregar(){
		if($this->input->method(TRUE) == "GET"){
			$productos = $this->productos->get_productos();
			$data = array(
				"rows"=>$productos,
				"urljs"=>"funciones_productos_destacados.js",
			);
			$this->load->view("admin/page_edit/agregar_producto_destacado",$data);
		}
		else if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$data = array(
				"id_producto"=>$id,
				"activo"=>1,
			);
			$this->utils->begin();
			$insert = $this->utils->insert($this->table,$data);
			if($insert){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo ingresado correctamente!";
			}
			else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al ingresar el registro";
			}
			echo json_encode($xdatos);
		}
	}

	function get_producto_autocomplete($query){
		echo $this->productos->get_producto_autocomplete($query);
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_producto_destacado ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->delete($this->table,$where);
			if($delete) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro eliminado con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser eliminado!";
			}
			echo json_encode($data);
		}
	}

	function state_change(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$active = $this->productos->get_state($id);
			if($active==1){
				$state = 0;
				$text = 'desactivado';
			}else{
				$state = 1;
				$text = 'activado';
			}
			$form = array(
				"activo" =>$state
			);
			$where = " id_producto_destacado ='".$id."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);
			if($update) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro $text con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser $text!";
			}
			echo json_encode($data);
			exit();
		}
	}



}

/* End of file Productos.php */
