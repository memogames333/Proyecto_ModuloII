<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sucursal extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "sucursal";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/SucursalModel","sucursal");
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Sucursales",
			"icono"=> "fa fa-building",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-plus",
					'url' => 'sucursal/agregar',
					'txt' => 'Agregar Sucursal',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Nombre"=>2,
				"Direccion"=>3,
				"Telefono"=>2,
				"WhatsApp"=>2,
				"Estado"=>1,
				"Acciones"=>1,
			),
		);
		$extras = array(
			'css' => array(),
			'js' => array(
				0 => "admin/js/funciones/funciones_sucursal.js"
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
			1 => 'descripcion',
			2 => 'direccion',
			3 => 'telefono1',
			4 => 'whatsapp',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->sucursal->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			$n=1;
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";
				$filename = base_url("admin/sucursal/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_sucursal. "' ><i class='fa fa-pencil' ></i> Editar</a></li>";

				$state = $rows->activo;
				if($state==1){
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-primary font-bold'>Activo<span>";
					$icon = "fa fa-toggle-off";
				}
				else{
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "fa fa-toggle-on";
				}
				$menudrop .= "<li><a class='state_change' data-state='$txt'  id=" . $rows->id_sucursal . " ><i class='$icon'></i> $txt</a></li>";

				$menudrop .= "<li><a class='delete_row'  id=" . $rows->id_sucursal . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";


				$data[] = array(
					$n,
					$rows->descripcion,
					$rows->direccion,
					$rows->telefono1,
					$rows->whatsapp,
					$show_text,
					$menudrop,
				);
				$n++;
			}
			$total = $this->sucursal->total_rows();
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

			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js',
					1 => 'admin/js/funciones/funciones_sucursal.js',
				),
			);
			layout("admin/page_edit/agregar_sucursal","",$extras);
		}
		else if($this->input->method(TRUE) == "POST"){

			$nombre = $this->input->post("nombre");
			$direccion = $this->input->post("direccion");
			$telefono1 = $this->input->post("telefono");
			$whatsapp = $this->input->post("whatsapp");

			$data = array(
				"descripcion"=>$nombre,
				"direccion"=>$direccion,
				"telefono1"=>$telefono1,
				"whatsapp"=>$whatsapp,
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

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(4);
			$row = $this->sucursal->get_row_info($id);
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
				);
				$extras = array(
					'css' => array(
						0 => 'admin/libs/dropify/dropify.min.css'
					),
					'js' => array(
						0 => 'admin/libs/dropify/dropify.min.js',
						1 => 'admin/js/funciones/funciones_sucursal.js'
					),
				);
				layout("admin/page_edit/editar_sucursal",$data,$extras);
			}else{
				redirect('admin/errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$nombre = $this->input->post("nombre");
			$direccion = $this->input->post("direccion");
			$telefono1 = $this->input->post("telefono");
			$id_sucursal = $this->input->post("id_sucursal");
			$whatsapp = $this->input->post("whatsapp");

			$where = " id_sucursal='".$id_sucursal."'";

			$data = array(
				"descripcion"=>$nombre,
				"direccion"=>$direccion,
				"telefono1"=>$telefono1,
				"whatsapp"=>$whatsapp,
			);
			$this->utils->begin();
			$insert = $this->utils->update($this->table,$data,$where);
			if($insert){
				$this->utils->commit();
				$xdatos["type"]="success";
				$xdatos['title']='Información';
				$xdatos["msg"]="Registo editado correctamente!";
			}
			else {
				$this->utils->rollback();
				$xdatos["type"]="error";
				$xdatos['title']='Alerta';
				$xdatos["msg"]="Error al editar el registro";
			}
			echo json_encode($xdatos);
		}
	}


	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_sucursal ='".$id."'";
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
			$active = $this->sucursal->get_state($id);
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
			$where = " id_sucursal ='".$id."'";
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
