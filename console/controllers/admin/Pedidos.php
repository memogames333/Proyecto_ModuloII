<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "orden";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/PedidosModel","pedidos");
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Pedidos",
			"icono"=> "fa fa-shopping-basket",
			"buttons" => array(	),
			"table"=>array(
				"N"=>1,
				"Fecha"=>2,
				"No. Orden"=>1,
				"Cliente"=>2,
				"Entrega"=>1,
				"Total"=>1,
				"Estado"=>1,
				"Última Act"=>2,
				"Acciones"=>1,
			),
		);
		$extras = array(
			'css' => array(),
			'js' => array(
				"admin/js/funciones/funciones_pedidos.js",
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
			1 => 'orden.id_orden',
			2 => 'orden.numero_orden',
			3 => 'orden.fecha',
			4 => 'u.nombre',
			5 => 'orden.entrega',
			6 => 'orden.total',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->pedidos->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			$n=1;
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-list-ul icon-white'></i> Menu <span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-menu-right' aria-labelledby='dLabel'>";

				$filename = base_url("admin/pedidos/ver/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->numero_orden. "' ><i class='fa fa-eye' ></i> Ver</a></li>";

				$n_orden = "<p class='font-bold text-primary'>$rows->numero_orden</p>";
				if($rows->finalizada==1){
					$estado = "<span class='badge font-bold' style='background-color:#28a745; color:#FFF;'>Finalizada</span>";
				}
				else if($rows->anulada==1){
					$estado = "<span class='badge badge-danger font-bold'>Anulada</span>";
				}
				else if($rows->cancelada==1){
					$estado = "<span class='badge badge-warning font-bold'>Cancelada</span>";
				}
				else{
					$estado = "<span class='badge badge-success font-bold'>En Proceso</span>";
					$menudrop .= "<li><a class='update_row' data-state=".$rows->id_estado."  data-id=". $rows->id_orden ." 	id='modal_btn_add' role='button' data-toggle='modal' data-target='#viewModal' data-refresh='true'><i class='fa fa-upload'></i> Actualizar</a></li>";
					$menudrop .= "<li><a class='cancel_row'  id=". $rows->id_orden ." ><i class='fa fa-trash'></i> Anular</a></li>";
				}

				$menudrop .= "</ul></div>";

				$act1= $rows->estado;
				if($rows->id_estado==0) $act1 = "PENDIENTE";
				$act = "<p class='font-bold text-primary'>$act1</p>";
				$data[] = array(
					$rows->id_orden,
					d_m_Y($rows->fecha),
					$n_orden,
					$rows->usuario,
					$rows->entrega,
					"$".dinero($rows->total),
					$estado,
					$act,
					$menudrop,
				);
				$n++;
			}
			$total = $this->pedidos->total_rows();
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

	function ver($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$numero_orden = $this->uri->segment(4);
			$row = $this->pedidos->get_row_info($numero_orden);
			if($row && $id!=""){
				$id_orden = $row->id_orden;
				$rows_detalle = $this->pedidos->get_products_list($id_orden);
				$count_product = $this->pedidos->count_products($id_orden);
				$data = array(
					"row"=>$row,
					"rows"=>$rows_detalle,
					"count"=>$count_product,
					"urljs"=>"funciones_pedidos.js",
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
					),
				);
				layout("admin/pedidos/ver_pedido",$data,$extras);
			}else{
				redirect('admin/errorpage');
			}
		}
		/*else if($this->input->method(TRUE) == "POST"){
			$descripcion = strtoupper($this->input->post("descripcion"));
			$desc_larga = strtoupper($this->input->post("desc_larga"));
			$marca = strtoupper($this->input->post("marca"));
			$id_categoria = $this->input->post("id_categoria");
			$precio = $this->input->post("precio");
			$tipo_producto = strtoupper($this->input->post("tipo_producto"));
			$id_producto = strtoupper($this->input->post("id_producto"));
			$where = " id_producto='".$id_producto."'";

			if ($_FILES["foto"]["name"] != "") {

				$_FILES['file']['name'] = $_FILES['foto']['name'];
				$_FILES['file']['type'] = $_FILES['foto']['type'];
				$_FILES['file']['tmp_name'] = $_FILES['foto']['tmp_name'];
				$_FILES['file']['error'] = $_FILES['foto']['error'];
				$_FILES['file']['size'] = $_FILES['foto']['size'];

				$config['upload_path'] = "./assets/img/productos/";
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
						'source_image' => './assets/img/productos/'.$name,
						'create_thumb' => FALSE,//tell the CI do not create thumbnail on image
						'maintain_ratio' => TRUE,
						'width' => 225,//new size of image
						'height' => 225,//new size of image
						'quality'=>100,
					);
					$this->image_lib->clear();
					$this->image_lib->initialize($img_array);
					$this->image_lib->resize();
					$url = 'img/productos/'.$name;
					$data = array(
						"descripcion"=>$descripcion,
						"desc_larga"=>$desc_larga,
						"marca"=>$marca,
						"id_categoria"=>$id_categoria,
						"precio"=>$precio,
						"tipo_producto"=>$tipo_producto,
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
				$data = array(
					"descripcion"=>$descripcion,
					"desc_larga"=>$desc_larga,
					"marca"=>$marca,
					"id_categoria"=>$id_categoria,
					"precio"=>$precio,
					"tipo_producto"=>$tipo_producto,
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
			}
			echo json_encode($xdatos);
		}*/
	}

	function cancel(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$form = array(
				"anulada"=>1,
				"finalizada"=>0,
				"cancelada"=>0,
			);
			$where = " id_orden ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->update($this->table,$form,$where);
			if($delete) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro anulado con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser anulado!";
			}
			echo json_encode($data);
		}
	}

	function update(){
		if($this->input->method(TRUE) == "GET"){

			$datos = explode("_",$this->uri->segment(4));
			$id_orden =$datos[0];
			$id_estado = $datos[1];
			$row = $this->pedidos->get_row_info_by_id($id_orden);
			$rows = $this->pedidos->get_estados_orden($id_estado);

			$datos = array(
				"id_orden"=>$id_orden,
				"row"=>$row,
				"id_estado"=>$id_estado,
				"rows"=>$rows,
			);

			$this->load->view("admin/pedidos/actualizar_estado",$datos);
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_estado = $this->input->post("id");
			$id_orden = $this->input->post("id_orden");
			if($id_estado==4){
				$form = array(
					"id_estado" =>$id_estado,
					"finalizada"=>1
				);
			}
			else{
				$form = array(
					"id_estado" =>$id_estado
				);
			}
			$where = " id_orden ='".$id_orden."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);
			if($update) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro actualizado con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser actualizado!";
			}
			echo json_encode($data);
		}
	}

	/*function state_change(){
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
	}*/



}

/* End of file Productos.php */
