<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "categoria";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/CategoriasModel","categorias");
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Categorias",
			"icono"=> "fa fa-server",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-plus",
					'url' => 'categorias/agregar',
					'txt' => 'Agregar Categoria',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Nombre"=>4,
				"Descripcion"=>4,
				"Acciones"=>1,
			),
			"urljs"=>"funciones_categorias.js",
		);
		$extras = array(
			'css' => array(),
			'js' => array(),
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
			0 => 'id_categoria',
			1 => 'nombre_cat',
			2 => 'descripcion',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->categorias->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";
					$filename = base_url("admin/subcategorias/").$rows->param;
				$menudrop .= "<li><a role='button' href='" . $filename. "' ><i class='fa fa-list' ></i> Subcategorias</a></li>";
				$filename = base_url("admin/categorias/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_categoria. "' ><i class='fa fa-pencil' ></i> Editar</a></li>";

				$menudrop .= "<li><a class='delete_row'  id=" . $rows->id_categoria . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";


				$data[] = array(
					$rows->id_categoria,
					$rows->nombre_cat,
					$rows->descripcion,
					$menudrop,
				);
			}
			$total = $this->categorias->total_rows();
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

	function agregar(){
		if($this->input->method(TRUE) == "GET"){
			$data = array(
				"urljs"=>"funciones_categorias.js",
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/productos/agregar_categoria",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));

			if ($_FILES["foto"]["name"] != "") {

				$_FILES['file']['name'] = $_FILES['foto']['name'];
				$_FILES['file']['type'] = $_FILES['foto']['type'];
				$_FILES['file']['tmp_name'] = $_FILES['foto']['tmp_name'];
				$_FILES['file']['error'] = $_FILES['foto']['error'];
				$_FILES['file']['size'] = $_FILES['foto']['size'];

				$config['upload_path'] = "./assets/img/categorias/";
				$config['allowed_types'] = 'jpg|jpeg|png|bmp';

				$info = new SplFileInfo( $_FILES['foto']['name']);
				$name = uniqid(date("dmYHi")).".".$info->getExtension();
				$config['file_name'] = $name;
				$this->upload->initialize($config);
				$this->load->library('upload', $config);

				if ($this->upload->do_upload('file')){
					$this->load->library('image_lib');
					$img_array = array(
						'image_library' => 'gd2',
						'source_image' => './assets/img/categorias/'.$name,
						'create_thumb' => FALSE,//tell the CI do not create thumbnail on image
						'maintain_ratio' => TRUE,
						'width' => 300,//new size of image
						'height' => 300,//new size of image
						'quality'=>100,
					);
					$this->image_lib->clear();
					$this->image_lib->initialize($img_array);
					$this->image_lib->resize();
					$url = 'img/categorias/'.$name;
					$param = replace_specials_characters($nombre);
					$data = array(
						"descripcion"=>$descripcion,
						"nombre_cat"=>$nombre,
						"param"=>url_title($param, '-', TRUE),
						"imagen"=>$url,
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
						$xdatos["msg"]="Error al ingresar el registro!";
					}
				}else{
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al ingresar el registro";
				}
			}
			else{
				$param = replace_specials_characters($nombre);
				$data = array(
					"descripcion"=>$descripcion,
					"nombre_cat"=>$nombre,
					"param"=>url_title($param, '-', TRUE),
				);
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
			}
			echo json_encode($xdatos);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(4);
			$row = $this->categorias->get_row_info($id);
			if($row && $id!=""){
				$data = array(
					"row"=>$row,
					"urljs"=>"funciones_categorias.js",
				);
				$extras = array(
					'css' => array(
						0 => 'admin/libs/dropify/dropify.min.css'
					),
					'js' => array(
						0 => 'admin/libs/dropify/dropify.min.js'
					),
				);
				layout("admin/productos/editar_categoria",$data,$extras);
			}else{
				redirect('admin/errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$descripcion = strtoupper($this->input->post("descripcion"));
			$nombre = strtoupper($this->input->post("nombre"));
			$id_categoria = strtoupper($this->input->post("id_categoria"));
			$where = " id_categoria='".$id_categoria."'";

			if ($_FILES["foto"]["name"] != "") {

				$_FILES['file']['name'] = $_FILES['foto']['name'];
				$_FILES['file']['type'] = $_FILES['foto']['type'];
				$_FILES['file']['tmp_name'] = $_FILES['foto']['tmp_name'];
				$_FILES['file']['error'] = $_FILES['foto']['error'];
				$_FILES['file']['size'] = $_FILES['foto']['size'];

				$config['upload_path'] = "./assets/img/categorias/";
				$config['allowed_types'] = 'jpg|jpeg|png|bmp';

				$info = new SplFileInfo( $_FILES['foto']['name']);
				$name = uniqid(date("dmYHi")).".".$info->getExtension();
				$config['file_name'] = $name;
				$this->upload->initialize($config);
				$this->load->library('upload', $config);

				if ($this->upload->do_upload('file')){
					$this->load->library('image_lib');
					$img_array = array(
						'image_library' => 'gd2',
						'source_image' => './assets/img/categorias/'.$name,
						'create_thumb' => FALSE,//tell the CI do not create thumbnail on image
						'maintain_ratio' => TRUE,
						'width' => 300,//new size of image
						'height' => 300,//new size of image
						'quality'=>100,
					);
					$this->image_lib->clear();
					$this->image_lib->initialize($img_array);
					$this->image_lib->resize();
					$url = 'img/categorias/'.$name;
					$param = replace_specials_characters($nombre);
					$data = array(
						"descripcion"=>$descripcion,
						"nombre_cat"=>$nombre,
						"param"=>url_title($param, '-', TRUE),
						"imagen"=>$url,
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
						$xdatos["msg"]="Error al editar el registro!";
					}
				}else{
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro";
				}
			}
			else{
				$param = replace_specials_characters($nombre);
				$data = array(
					"descripcion"=>$descripcion,
					"nombre_cat"=>$nombre,
					"param"=>url_title($param, '-', TRUE),
				);
				$this->utils->begin();
				$insert = $this->utils->update($this->table,$data,$where);
				if($insert){
					$this->utils->commit();
					$xdatos["param"]=$param;
					$xdatos["url"]=url_title($param, '-', TRUE);
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
			}
			echo json_encode($xdatos);
		}
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_categoria ='".$id."'";
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
			$id_client = $this->input->post("id");
			$active = $this->productos->get_state($id_client);
			if($active==1){
				$state = 0;
				$text = 'activado';
			}else{
				$state = 1;
				$text = 'desactivado';
			}
			$form = array(
				"inactivo" =>$state
			);
			$where = " id_producto ='".$id_client."'";
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
