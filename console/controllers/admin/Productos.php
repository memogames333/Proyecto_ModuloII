<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/ProductosModel","productos");
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
		$this->load->helper('upload_file_helper');
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Productos",
			"icono"=> "fa fa-cubes",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-plus",
					'url' => 'productos/agregar',
					'txt' => 'Agregar Producto',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Nombre"=>3,
				"Marca"=>2,
				"Categoria	"=>2,
				"Precio"=>1,
				// "Stock"=>1,
				"Estado"=>1,
				"Acciones"=>1,
			),
			"urljs"=>"funciones_productos.js",
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
			0 => 'producto.id_producto',
			1 => 'producto.descripcion',
			2 => 'producto.marca',
			3 => 'c.nombre_cat',
			4 => 'producto.precio',
			5 => 'producto.tipo_producto',
			6 => 'producto.stock',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->productos->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";
					$filename = base_url("admin/productos/editar/");
					$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_producto. "' ><i class='fa fa-pencil' ></i> Editar</a></li>";

				$state = $rows->inactivo;
				if($state==0){
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-primary font-bold'>Activo<span>";
					$icon = "fa fa-toggle-off";
				}
				else{
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "fa fa-toggle-on";
				}
				$menudrop .= "<li><a class='state_change' data-state='$txt'  id=" . $rows->id_producto . " ><i class='$icon'></i> $txt</a></li>";

				if($rows->tipo_producto == "FISICO" && !$rows->talla)
				{
					$menudrop .= "<li><a class='insert_row' href='productos/stock/".$rows->id_producto."' data-toggle='modal' data-target='#viewModal' data-refresh='true'><i class='fa fa-plus'></i> Gestionar inventario</a></li>";
				}
				$stock = round($rows->stock,0);
				$cnt = $stock;
				if($rows->talla)
				{
					$stocks = $this->productos->stock_tallas($rows->id_producto);
					$stock = "";
					$us = 0;
					// print_r($stocks);
					if($stocks != NULL)
					{
						foreach ($stocks as $std)
						{
							$us += $std->cantidad;
							$stock.=  $std->talla." -> ".$std->cantidad."<br>";
						}
					}
					$stock.=  "TTL -> ".$us."<br>";
					$cnt = $us;
					//$stock = $stocks->cantidad;
				}
				$menudrop .= "<li><a class='delete_row' canti='".$cnt."'  id=" . $rows->id_producto . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

				$data[] = array(
					$rows->id_producto,
					$rows->descripcion,
					$rows->marca,
					$rows->categoria,
					"$".dinero($rows->precio),
					// $stock,
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

	function stock($id){
		if($this->input->method(TRUE) == "GET")
		{
			$row = $this->productos->get_row_info($id);
			$descripcion = $row->descripcion;
			$stock = $row->stock;
			$precio = $row->precio;
			$data = array(
				"id"=>$id,
				"descripcion"=>$descripcion,
				"stock"=>$stock,
				"precio"=>$precio,
			);
			$this->load->view("admin/productos/cargar_stock",$data);
		}
	}

	function agregar(){
		if($this->input->method(TRUE) == "GET")
		{
			$tipo = $this->productos->get_tipo();
			$categorias = $this->productos->get_categorias();
			$id = 0;
			$f = 1;
			foreach ($categorias as $cat)
			{
				if($f)
				{
					$id = $cat->id_categoria;
					$f=0;
				}
			}
			$subcategorias = $this->productos->getSubcategorias($id);
			$data = array(
				"categorias"=>$categorias,
				"tipo"=>$tipo,
				"subcategorias"=>$subcategorias,
				"urljs"=>"funciones_productos.js",
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/productos/agregar_producto",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST")
		{
			$descripcion_nombre = strtoupper($this->input->post("descripcion_nombre"));
			$desc_larga = $this->input->post("desc_larga");
			$marca = strtoupper($this->input->post("marca"));
			$id_categoria = $this->input->post("id_categoria");
			$id_subcategoria = $this->input->post("id_subcategoria");
			$precio = $this->input->post("precio");
			$ttalla = $this->input->post("ttalla");
			$talla = 0;
			if($ttalla == "Si")
			{
				$talla = 1;
			}
			$stock = $this->input->post("stock");
			$lista_array = $this->input->post("listas_array");
			$tipo_producto = strtoupper($this->input->post("tipo_producto"));
			$upload_path = "assets/img/productos/";
			$upload_path_thumb = "assets/img/productos/thumb/";

			$path = "img/productos/";
			$path_thumb = "img/productos/thumb/";

			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img1=$path.$imagen;
				$up_img1_thumb=$path_thumb.$imagen;
			}
			else{
				$up_img1 = "";
				$up_img1_thumb="";
			}

			if ($_FILES["img2"]["name"] != "") {
				$imagen = upload_image("img2",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img2=$path.$imagen;
			}
			else $up_img2 = "";

			if ($_FILES["img3"]["name"] != "") {
				$imagen = upload_image("img3",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img3=$path.$imagen;
			}
			else $up_img3 = "";

			if ($_FILES["img4"]["name"] != "") {
				$imagen = upload_image("img4",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img4=$path.$imagen;
			}
			else $up_img4 = "";

			$data = array(
				"descripcion"=>$descripcion_nombre,
				"desc_larga"=>$desc_larga,
				"marca"=>$marca,
				"id_categoria"=>$id_categoria,
				"id_subcategoria"=>$id_subcategoria,
				"precio"=>$precio,
				"tipo_producto"=>$tipo_producto,
				"imagen"=>$up_img1,
				"talla"=>$talla,
				"stock"=>$stock,
				"thumb1"=>$up_img1_thumb,
				"img2"=>$up_img2,
				"img3"=>$up_img3,
				"img4"=>$up_img4,
			);
			$this->utils->begin();
			$insert = $this->utils->insert($this->table,$data);
			if($insert)
			{
				if($ttalla)
				{
					$id_producto = $this->utils->insert_id();
					$tabla_aux = "producto_talla";
					$n=0;
					$array = json_decode($lista_array, true);
					foreach ($array as $fila)
					{
						$titulo = $fila["titulo"];
						$color1 = $fila["color"];
						$caracteristica = $fila["caracteristica"];
						$lista = $fila["lista"];
						$tabX = "producto_tipo";
						$form_datos = array(
							'id_producto' => $id_producto,
							'id_tipo' => $caracteristica,
							'descripcion' => $titulo,
							'color' => $color1,
						);
						$insert = $this->utils->insert($tabX, $form_datos);
						$id_de = $this->utils->insert_id();
						$array_datos = json_decode($lista, true);
						foreach ($array_datos as $key)
						{
							$aumento = $key["aumento"];
							$existencia = $key["existencia"];
							$descripcion2 = $key["descripcion"];
							$nombre_color = $key["nombre_color"];

							$tab_item = "producto_tipo_detalle";
							$lis = array(
								'id_pt' => $id_de,
								'aumento' => $aumento,
								'descripcion' => $descripcion2,
								'cantidad' => $existencia,
								'nombre_color' => $nombre_color,
							);
							$insert = $this->utils->insert($tab_item, $lis);
						}

						// $lista = array(
						// 	'id_producto' => $id_producto,
						// 	'talla' => $tall,
						// 	'cantidad' => $cant,
						// );
						// $ins = $this->utils->insert($tabla_aux, $lista);
						// if(!$ins)
						// {
						// 	$n = 1;
						// }
					}
				}
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
			//}
			echo json_encode($xdatos);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET")
		{
			$id = $this->uri->segment(4);
			$row = $this->productos->get_row_info($id);
			if($row && $id!=""){
				$subcategorias = $this->productos->getSubcategorias($row->id_categoria);
				$categorias = $this->productos->get_categorias();
				$tallas = $this->productos->get_tallas($id);
				$precio = $row->precio;
				$tipo_producto = $row->tipo_producto;
				$imagen = $row->imagen;
				$tipo = $this->productos->get_tipo();
				$tipos_list = $this->productos->tipos($id);
				$list = "";
				// print_r($tipo_list);
				if(empty($tipo_list))
				{
					foreach ($tipos_list as $key)
					{
						$id_tipos = $key["id"];
						$descripcion = $key["descripcion"];
						$color = $key["color"];
						$list .= '<div class="col-lg-4">';
						$list .= '	<table class="table table-bordered table-hover table-striped tab-datos">';
						$list .= '		<thead>';
						if($color == 1)
						{
							$list .= '			<tr>';
							$list .= '				<th class="col-lg-5" style="text-align:center;" colspan="4"><input type="hidden" class="form-control" id="id_tipop" name="id_tipop" value="'.$id_tipos.'"><input type="hidden" class="form-control" id="c_caracteristica" name="c_caracteristica" value="'.$id_tipos.'"><input type="hidden" class="form-control" id="color_exist" name="color_exist" value="1"><input type="text" class="form-control" id="titulo_c" name="titulo_c" placeholder="Nombre caracteristica" value="'.$descripcion.'"></th>';
							$list .= '			</tr>';
							$list .= '			<tr>';
							$list .= '				<th class="col-lg-5">Color</th>';
							$list .= '				<th class="col-lg-5">Nombre</th>';
							$list .= '				<th class="col-lg-5">Aumento</th>';
							// lista += '				<th class="col-lg-5">Existencia</th>';
							$list .= '				<th class="col-lg-2">Acciones</th>';
						}
						else
						{
							$list .= '			<tr>';
							$list .= '				<th class="col-lg-5" style="text-align:center;" colspan="4"><input type="hidden" class="form-control" id="id_tipop" name="id_tipop" value="'.$id_tipos.'"><input type="hidden" class="form-control" id="c_caracteristica" name="c_caracteristica" value="'.$id_tipos.'"><input type="hidden" class="form-control" id="color_exist" name="color_exist" value="0"><input type="text" class="form-control" id="titulo_c" name="titulo_c" placeholder="Nombre caracteristica" value="'.$descripcion.'"></th>';
							$list .= '			</tr>';
							$list .= '			<tr>';
							$list .= '				<th class="col-lg-5">Descripción</th>';
							$list .= '				<th class="col-lg-5">Aumento</th>';
							// $list .= '				<th class="col-lg-5">Existencia</th>';
							$list .= '				<th class="col-lg-2">Acciones</th>';
						}
						$list .= '			</tr>';
						$list .= '		</thead>';
						$list .= '		<tbody id="color_table" class="tab_body">';

						$detalles = $this->productos->detalles_tipo($id_tipos);
						foreach ($detalles as $key1)
						{
							$id_detalle = $key1["id_detalle"];
							$descrip = $key1["descripcion"];
							$aumento = $key1["aumento"];
							$nombre_color = $key1["nombre_color"];
							if($color == 1)
							{
								$list .= '		<tr class="fila">';
								$list .= '				<td class="col-lg-5"><input type="hidden" id="id_detalle" class="id_detalle" value="'.$id_detalle.'"><input type="text" id="custom" class="custom" /><input type="hidden" id="descripcion" name="descripcion" value="'.$descrip.'"></td>';
								$list .= '				<td class="col-lg-5"><input type="text" class="form-control" id="nombre_color" name="nombre_color" value="'.$nombre_color.'"></td>';
								$list .= '				<td class="col-lg-5"><input type="text" class="form-control decimal1" id="aumento" name="aumento" value="'.$aumento.'"></td>';
								// lista += '				<td class="col-lg-5"><input type="text" class="form-control decimal2" id="existencia" name="existencia"></td>';
								$list .= '				<td class="col-lg-2"><a class="btn btn-danger deltre" idt="15"><i class="fa fa-trash"></i></a></td>';
								$list .= '			</tr>';
							}
							else
							{
								$list .= '		<tr class="fila">';
									$list .= '				<td class="col-lg-5"><input type="hidden" id="id_detalle" class="id_detalle" value="'.$id_detalle.'"><input type="text" id="descripcion" name="descripcion" class="form-control" value="'.$descrip.'"></td>';
									$list .= '				<td class="col-lg-5"><input type="text" class="form-control decimal1" id="aumento" name="aumento" value="'.$aumento.'"></td>';
									// $list .= '				<td class="col-lg-5"><input type="text" class="form-control decimal2" id="existencia" name="existencia"></td>';
									$list .= '				<td class="col-lg-2"><a class="btn btn-danger deltre" idt="15"><i class="fa fa-trash"></i></a></td>';
									$list .= '			</tr>';
								}
							}
							$list .= '		</tbody>';
							$list .= '		<tfooter>';
								$list .= '			<tr>';
									$list .= '				<th class="col-lg-5" style="text-align:center;" colspan="4"><a id="n_item_color">Nuevo item</a></th>';
									$list .= '			</tr>';
									$list .= '		</tfooter>';
									$list .= '	</table>';
									$list .= '</div>';
								}
				}
				$data = array(
					"row"=>$row,
					"tallas"=>$tallas,
					"precio"=>$precio,
					"imagen"=>$imagen,
					"tipo_producto"=>$tipo_producto,
					"categorias"=>$categorias,
					"subcategorias"=>$subcategorias,
					"tipo"=>$tipo,
					"list"=>$list,
					"tipos_list"=>$tipos_list,
					"urljs"=>"funciones_productos.js",
				);
				$extras = array(
					'css' => array(
						0 => 'admin/libs/dropify/dropify.min.css'
					),
					'js' => array(
						0 => 'admin/libs/dropify/dropify.min.js'
					),
				);
				layout("admin/productos/editar_producto",$data,$extras);
			}else{
				redirect('admin/errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$descripcion = strtoupper($this->input->post("descripcion_nombre"));
			$desc_larga = $this->input->post("desc_larga");
			$marca = strtoupper($this->input->post("marca"));
			$id_categoria = $this->input->post("id_categoria");
			$id_subcategoria = $this->input->post("id_subcategoria");
			$precio = $this->input->post("precio");
			$ttalla = $this->input->post("ttalla");
			$talla = 0;
			if($ttalla == "SI")
			{
				$talla = 1;
			}
			$lista_array = $this->input->post("listas_array");
			$stock = $this->input->post("stock");
			$tallas = $this->input->post("tallas");
			$tipo_producto = strtoupper($this->input->post("tipo_producto"));
			$id_producto = strtoupper($this->input->post("id_producto"));
			$row = $this->productos->get_row_info($id_producto);
			$where = " id_producto='".$id_producto."'";
			$upload_path = "assets/img/productos/";
			$upload_path_thumb = "assets/img/productos/thumb/";

			$path = "img/productos/";
			$path_thumb = "img/productos/thumb/";
			if ($_FILES["foto"]["name"] != "") {
				$imagen = upload_image("foto",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img1=$path.$imagen;
				$up_img1_thumb=$path_thumb.$imagen;
			}
			else{
				$up_img1 = $row->imagen;
				$up_img1_thumb=$row->thumb1;
			}
			if ($_FILES["img2"]["name"] != "") {
				$imagen = upload_image("img2",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img2=$path.$imagen;
			}
			else $up_img2 = $row->img2;
			if ($_FILES["img3"]["name"] != "") {
				$imagen = upload_image("img3",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img3=$path.$imagen;
			}
			else $up_img3 = $row->img3;
			if ($_FILES["img4"]["name"] != "") {
				$imagen = upload_image("img4",$upload_path);
				resize_image($imagen, $upload_path,1000,1000,100,0,"");
				resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$up_img4=$path.$imagen;
			}
			else $up_img4 = $row->img4;

			$data = array(
				"descripcion"=>$descripcion,
				"desc_larga"=>$desc_larga,
				"marca"=>$marca,
				"id_categoria"=>$id_categoria,
				"id_subcategoria"=>$id_subcategoria,
				"precio"=>$precio,
				"talla"=>$talla,
				"stock"=>$stock,
				"tipo_producto"=>$tipo_producto,
				"imagen"=>$up_img1,
				"thumb1"=>$up_img1_thumb,
				"img2"=>$up_img2,
				"img3"=>$up_img3,
				"img4"=>$up_img4,
			);
			$this->utils->begin();
			$insert = $this->utils->update($this->table,$data,$where);
			if($insert){
				if($ttalla)
				{
					// $tabla_aux = "producto_talla";
					// //$this->utils->delete($tabla_aux,$where);
					// $n=0;
					// $array = json_decode($tallas, true);
					// foreach ($array as $fila)
					// {
					// 	$id = $fila["id"];
					// 	$tall = $fila["tall"];
					// 	$cant = $fila["cant"];
					//
					// 	$lista = array(
					// 		'id_producto' => $id_producto,
					// 		'talla' => $tall,
					// 		'cantidad' => $cant,
					// 	);
					// 	if($id != "")
					// 	{
					// 		$wherel = "id_talla='".$id."'";
					// 		$ins = $this->utils->update($tabla_aux, $lista,$wherel);
					// 	}
					// 	else {
					// 		$ins = $this->utils->insert($tabla_aux, $lista);
					// 	}
					// 	if(!$ins)
					// 	{
					// 		$n = 1;
					// 	}
					// }
					// $id_producto = $this->utils->insert_id();
					$tabla_aux = "producto_talla";
					$n=0;
					$array = json_decode($lista_array, true);
					foreach ($array as $fila)
					{
						$titulo = $fila["titulo"];
						$color1 = $fila["color"];
						$caracteristica = $fila["caracteristica"];
						$id_tipop = $fila["id_tipop"];
						$lista = $fila["lista"];
						$tabX = "producto_tipo";
						if($id_tipop != "")
						{
							// echo "EntraX";
							$form_datos = array(
								// 'id_producto' => $id_producto,
								'id_tipo' => $caracteristica,
								'descripcion' => $titulo,
								// 'color' => $color1,
							);
							$wd = "id='".$id_tipop."'";
							$insert = $this->utils->update($tabX, $form_datos, $wd);
							$id_de = $id_tipop;
						}
						else
						{
							// echo "EntraXD";
							// echo $id_producto;
							$form_datos = array(
								'id_producto' => $id_producto,
								'id_tipo' => $caracteristica,
								'descripcion' => $titulo,
								'color' => $color1,
							);
							$insert = $this->utils->insert($tabX, $form_datos);
							$id_de = $this->utils->insert_id();
						}
						$array_datos = json_decode($lista, true);
						foreach ($array_datos as $key)
						{
							$aumento = $key["aumento"];
							// $existencia = $key["existencia"];
							$descripcion2 = $key["descripcion"];
							$nombre_color = $key["nombre_color"];
							$id_detalle = $key["id_detalle"];
							if($id_detalle != "")
							{
								$tab_item = "producto_tipo_detalle";
								$lis = array(
									'id_pt' => $id_de,
									'aumento' => $aumento,
									'descripcion' => $descripcion2,
									// 'cantidad' => $existencia,
									'nombre_color' => $nombre_color,
								);
								$wdx = "id_detalle='".$id_detalle."'";
								$insert = $this->utils->update($tab_item, $lis, $wdx);
							}
							else
							{
								$tab_item = "producto_tipo_detalle";
								$lis = array(
									'id_pt' => $id_de,
									'aumento' => $aumento,
									'descripcion' => $descripcion2,
									// 'cantidad' => $existencia,
									'nombre_color' => $nombre_color,
								);
								$insert = $this->utils->insert($tab_item, $lis);
							}
						}

						// $lista = array(
						// 	'id_producto' => $id_producto,
						// 	'talla' => $tall,
						// 	'cantidad' => $cant,
						// );
						// $ins = $this->utils->insert($tabla_aux, $lista);
						// if(!$ins)
						// {
						// 	$n = 1;
						// }
					}
				}
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
			$where = " id_producto ='".$id."'";
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
	function deletetalla(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_talla ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->delete("producto_talla",$where);
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
	public function subcategorias()
	{
		$id = $_POST["id"];
		$subs = $this->productos->getSubcategorias($id);
		if($subs != null)
		{
			$xdatos["typeinfo"] = "success";
			$list = "<option value=''>Seleccione</option>";
			foreach ($subs as $sub)
			{
				$list .= "<option value='".$sub->id_subcategoria."'>".$sub->nombre_cat."</option>";
			}
		}
		else {
			$xdatos["typeinfo"] = "error";
			$list = "<option value=''>No se encontraron subcategorias</option>";
		}
		$xdatos["list"] = $list;
		echo json_encode($xdatos);
	}

	public function tipo_categoria()
	{
		$id = $this->input->post("id");
		$color = $this->productos->color($id);
		$lista = "";
		if($color == 1)
		{
			$xdatos["type"] = "Success";
		}
		else
		{
			$xdatos['type'] = "Fail";
		}
		echo json_encode($xdatos);
	}
}

/* End of file Productos.php */
