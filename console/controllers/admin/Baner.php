<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Baner extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "baner";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/BanerModel","baner");
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
		$this->load->helper('upload_file_helper');
		$this->load->helper('num2letras');
		$this->load->dbutil();
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Administrar Baner",
			"icono"=> "fa fa-cubes",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-plus",
					'url' => 'baner/baner',
					'txt' => ' Agregar baner',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Titulo"=>3,
				"Descripción"=>7,
				"Acciones"=>1,
			),
			"urljs"=>"funciones_baner.js",
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
			0 => 'baner.id_baner',
			1 => 'baner.img',
			2 => 'baner.titulo',
			3 => 'baner.descripcion',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->baner->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";
					$filename = base_url("admin/baner/editar_baner/");
					$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_baner. "'><i class='fa fa-edit' ></i> Editar</a></li>";

				  $menudrop .= "<li><a class='delete_row'  id=" . $rows->id_baner . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

        // $dia = substr($rows->fecha,8,2);
        // $mes = substr($rows->fecha,5,2);
        // $a = substr($rows->fecha,0,4);
        // $fecha = "$dia-$mes-$a";

				$data[] = array(
					$rows->id_baner,
					$rows->titulo,
					$rows->descripcion,
					$menudrop,
				);
			}
			$total = $this->baner->total_rows();
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
				"",
				"No se encontraron registros",
				"",
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

	function baner()
	{
		if($this->input->method(TRUE) == "GET")
		{
			// $categorias = $this->inventario->get_categorias();
			$data = array(
				// "categorias"=>$categorias,
				"urljs"=>"funciones_baner.js",
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/baner/agregar_baner",$data,$extras);
		}
	}
	function editar_baner($id)
	{
		if($this->input->method(TRUE) == "GET")
		{
			$row = $this->baner->get_baner($id);
      $descripcion = $row->descripcion;
      $img = $row->img;
      $titulo = $row->titulo;
      $color = $row->color;
			$data = array(
				// "categorias"=>$categorias,
				"urljs"=>"funciones_baner.js",
				"descripcion"=>$descripcion,
				"img"=>$img,
				"titulo"=>$titulo,
				"color"=>$color,
				"id_baner"=>$id,
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/baner/editar_baner",$data,$extras);
		}
	}

  function baner_editar()
  {
    if($this->input->method(TRUE) == "POST")
		{
      $id_baner = $this->input->post("id_baner");
			$descripcion = $this->input->post("descripcion");
			$titulo = $this->input->post("titulo");
			$color = $this->input->post("color");
			$imagen_ant = $this->input->post("imagen_ant");

			$upload_path = "assets/img/slider/";
			// $upload_path_thumb = "assets/img/productos/thumb/";

			$path = "img/slider/";
			// $path_thumb = "img/productos/thumb/";

			if ($_FILES["baner"]["name"] != "")
      {
				$imagen = upload_image("baner",$upload_path);
				resize_image($imagen, $upload_path,1200,700,100,0,"");
				// resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$imagen=$path.$imagen;
				// $up_img1_thumb=$path_thumb.$imagen;
        $data = array(
  				"descripcion"=>$descripcion,
  				"titulo"=>$titulo,
  				"color"=>$color,
  				"img"=>$imagen,
  			);
  			$this->utils->begin();
        $where = " id_baner='".$id_baner."'";
  			$update = $this->utils->update($this->table,$data, $where);

  			if($update){
  				$this->utils->commit();
  				$xdatos["type"]="success";
  				$xdatos['title']='Información';
  				$xdatos["msg"]="Registo ingresado correctamente!";
          unlink("assets/".$imagen_ant);
  			}
  			else {
  				$this->utils->rollback();
  				$xdatos["type"]="error";
  				$xdatos['title']='Alerta';
  				$xdatos["msg"]="Error al ingresar el registro";
  			}
			}
			else
      {
        $data = array(
  				"descripcion"=>$descripcion,
  				"titulo"=>$titulo,
  				"color"=>$color,
  			);
  			$this->utils->begin();
        $where = " id_baner='".$id_baner."'";
  			$update = $this->utils->update($this->table,$data, $where);

        if($update){
  				$this->utils->commit();
  				$xdatos["type"]="success";
  				$xdatos['title']='Información';
  				$xdatos["msg"]="Registo ingresado correctamente!";
          // unlink("assets/".$imagen_ant);
  			}
  			else {
  				$this->utils->rollback();
  				$xdatos["type"]="error";
  				$xdatos['title']='Alerta';
  				$xdatos["msg"]="Error al ingresar el registro";
  			}
			}
			//}
			echo json_encode($xdatos);
		}
  }

  function fetch($query)
	{
		// $id_sucursal=$this->session->id_sucursal;
		$this->load->model('InventarioModel');
		echo $this->InventarioModel->traer_cliente($query);
	}

	function cargar_producto()
	{
		if($this->input->method(TRUE) == "POST")
		{
			$id_producto = $this->input->post("id_producto");

			$this->load->model('InventarioModel');
			echo $this->InventarioModel->consultar_prod($id_producto);
		}
	}

  function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_baner ='".$id."'";
      $numero = $this->baner->get_num();
      if($numero > 1)
      {
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
      }
      else
      {
        $data["type"] = "Num_null";
        $data["title"] = "Alerta!";
        $data["msg"] = "Solo existe un baner en el registro por lo tando no se puede eliminar!";
      }

			echo json_encode($data);
		}
	}

	function stock_csv()
	{
		if($this->input->method(TRUE) == "GET")
		{
			// $categorias = $this->inventario->get_categorias();
			$data = array(
				// "categorias"=>$categorias,
				"urljs"=>"funciones_inventario.js",
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/inventario/inventario_csv",$data,$extras);
		}
	}

  function agregar(){
		if($this->input->method(TRUE) == "POST")
		{
			$descripcion = $this->input->post("descripcion");
			$titulo = $this->input->post("titulo");
			$color = $this->input->post("color");

			$upload_path = "assets/img/slider/";
			// $upload_path_thumb = "assets/img/productos/thumb/";

			$path = "img/slider/";
			// $path_thumb = "img/productos/thumb/";

			if ($_FILES["baner"]["name"] != "") {
				$imagen = upload_image("baner",$upload_path);
				resize_image($imagen, $upload_path,1200,700,100,0,"");
				// resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$imagen=$path.$imagen;
				// $up_img1_thumb=$path_thumb.$imagen;
        $data = array(
  				"descripcion"=>$descripcion,
  				"titulo"=>$titulo,
  				"color"=>$color,
  				"img"=>$imagen,
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
			}
			else
      {
        $xdatos["type"]="error";
        $xdatos['title']='Alerta';
        $xdatos["msg"]="Error al ingresar el registro";
			}
			//}
			echo json_encode($xdatos);
		}
	}

}

/* End of file Inventario.php */
