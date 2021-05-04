<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventario extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "movimiento_producto";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/InventarioModel","inventario");
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
			"titulo"=> "Administrar Movimientos",
			"icono"=> "fa fa-cubes",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-upload",
					'url' => 'inventario/carga',
					'txt' => ' Cargar Inventario',
					'modal' => false,
				),
				1 => array(
					"icon"=> "fa fa-download",
					'url' => 'inventario/descarga',
					'txt' => ' Descargar Inventario',
					'modal' => false,
				),
				2 => array(
					"icon"=> "fa fa-balance-scale",
					'url' => 'inventario/ajuste',
					'txt' => ' Ajuste Inventario',
					'modal' => false,
				),
				/*3 => array(
					"icon"=> "fa fa-file-excel-o",
					'url' => 'inventario/stock_csv',
					'txt' => ' Cargar CSV',
					'modal' => false,
				),*/
			),
			"table"=>array(
				"ID"=>1,
				"Fecha"=>1,
				"Concepto"=>7,
				"Tipo	"=>2,
				"Acciones"=>1,
			),
			"urljs"=>"funciones_inventario.js",
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
			0 => 'movimiento_producto.id_movimiento',
			1 => 'movimiento_producto.fecha',
			2 => 'movimiento_producto.concepto',
			3 => 'movimiento_producto.tipo',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->inventario->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";
					$filename = base_url("admin/inventario/ver_detalle/");
					$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_movimiento. "' data-toggle='modal' data-target='#viewModal' data-refresh='true'><i class='fa fa-search' ></i> Ver detalle</a></li>";

				// $menudrop .= "<li><a class='delete_row'  id=" . $rows->id_movimiento . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";

        $dia = substr($rows->fecha,8,2);
        $mes = substr($rows->fecha,5,2);
        $a = substr($rows->fecha,0,4);
        $fecha = "$dia-$mes-$a";

				$data[] = array(
					$rows->id_movimiento,
					$fecha,
					$rows->concepto,
					$rows->tipo,
					$menudrop,
				);
			}
			$total = $this->inventario->total_rows();
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

	function carga()
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
			layout("admin/inventario/carga_inventario",$data,$extras);
		}
	}

	function descarga(){
		if($this->input->method(TRUE) == "GET")
		{
			// $categorias = $this->inventario->get_categorias();
			$data = array(
				// "categorias"=>$categorias,
				"urljs"=>"funciones_descarga.js",
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/inventario/descarga_inventario",$data,$extras);
		}
	}
	function ajuste(){
		if($this->input->method(TRUE) == "GET")
		{
			// $categorias = $this->inventario->get_categorias();
			$data = array(
				// "categorias"=>$categorias,
				"urljs"=>"funciones_ajuste.js",
			);
			$extras = array(
				'css' => array(
					0 => 'admin/libs/dropify/dropify.min.css'
				),
				'js' => array(
					0 => 'admin/libs/dropify/dropify.min.js'
				),
			);
			layout("admin/inventario/ajuste_inventario",$data,$extras);
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
			$prod = $this->InventarioModel->consultar_prod($id_producto);
			echo $prod;
		}
	}
	function stock_talla()
	{
		if($this->input->method(TRUE) == "POST")
		{
			$id = $this->input->post("id");
			$this->load->model('InventarioModel');
			$prod = $this->InventarioModel->get_stock_talla($id);
			echo json_encode($prod);
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
	function ver_detalle($id_mov){
		if($this->input->method(TRUE) == "GET")
		{
			$detalles=$this->inventario->detalles($id_mov);
			$detalle = array();
			foreach ($detalles as $det)
			{
				$tallas = $this->inventario->get_stock_talla($det["id_presentacion"]);
				$det["talla"] = $tallas;
				array_push($detalle,$det);
			}
			$row = $this->inventario->get_movimiento($id_mov);
			$concepto = $row->concepto;
			$total = $row->total;
			$tipo = $row->tipo;
			$fecha = d_m_Y($row->fecha);
			$data = array(
				"detalle"=>$detalle,
				"concepto"=>$concepto,
				"total"=>$total,
				"tipo"=>$tipo,
				"fecha"=>$fecha,
			);
			// echo $id_mov;
			// print_r($data);
			$this->load->view("admin/inventario/ver_detalle",$data);
		}
	}

	function ingreso()
	{
		if($this->input->method(TRUE) == "POST")
		{
			// $id_producto = $this->input->post("id_producto");

			$cuantos = $this->input->post('cuantos');
	    $fecha_movimiento= Y_m_d($this->input->post('fecha_movimiento'));
	    $total_compras = $this->input->post('total_compras');
	    $array_json=$this->input->post('json_arr');

			if ($cuantos>0)
			{
        $this->utils->begin();
        $hora=date("H:i:s");
        $fecha_ing=date('Y-m-d');


        $table_y='movimiento_producto';
        $form_data = array(
          'concepto' => "CARGA DE INVENTARIO",
          'total' => $total_compras,
          'tipo' => 'ENTRADA',
          'proceso' => 'II',
          //'referencia' => $numero_doc,
          'fecha' => $fecha_movimiento,
          'hora' => $hora,
          //'id_proveedor' => $id_proveedor,
          //'id_compra' => $id_fact,
        );
        $insert_mov = $this->utils->insert($table_y,$form_data);
        $id_movimiento=$this->utils->insert_id();

        $j = 1 ;
        $k = 1 ;
        $l = 1 ;
        $m = 1 ;
        $d = 1 ;
        $b = 1 ;

        $array = json_decode($array_json, true);
        foreach ($array as $fila)
				{
          $id_producto=$fila['id_producto'];
          $cantidad=$fila['cantidad'];
          $precio_compra=$fila['precio_compra'];
          $detalle=$fila['detalle'];
          // $precio_venta = $fila["precio_venta"];
          $suu = round($cantidad * $precio_compra, 3);
          $id_su="";
          /*cantidad de una presentacion por la unidades que tiene*/


          $cantidad=$cantidad;


          // $sql2="SELECT existencia FROM stock WHERE id_producto='$id_producto'";
          // $stock2=_query($sql2);
          // $row2=_fetch_array($stock2);
          // $nrow2=_num_rows($stock2);
					if($talla > 0)
					{
						$rowx = $this->inventario->get_stock_talla($talla);
						$existencias = $rowx->cantidad;
					}
					else {
						$rowx = $this->inventario->get_row_info($id_producto);
						$existencias = $rowx->stock;
					}
          $table1= 'movimiento_producto_detalle';
          $cant_total=$cantidad+$existencias;
          $form_data1 = array(
            'id_movimiento'=>$id_movimiento,
            'id_producto' => $id_producto,
            'id_presentacion' => $talla,
            'cantidad' => $cantidad,
            'costo' => $precio_compra,
            'precio' => $precio_compra,
            'stock_anterior'=>$existencias,
            'stock_actual'=>$cant_total,
            //'id_presentacion' => $id_presentacion,
          );
          $insert_mov_det = $this->utils->insert($table1,$form_data1);
          if(!$insert_mov_det)
          {
            $j = 0;
          }

					$table2= 'stock';
					$nrow2 = $this->inventario->verificar($id_producto, $detalle);
					if($nrow2==0)
					{
						$cant_total=$cantidad;
						$form_data2 = array(
							'id_producto' => $id_producto,
							'stock' => $cant_total,
							'costo_unitario'=>$precio_compra,
							'precio_unitario'=>$precio_compra,
							'create_date'=>$fecha_movimiento,
							'update_date'=>$fecha_movimiento,
							'caracteristicas'=>$detalle,
							// 'id_sucursal' => $id_sucursal
						);
						$insert_stock = $this->utils->insert($table2,$form_data2 );
					}
					else
					{
						$cant_total=$cantidad+$existencias;
						$form_data2 = array(
							'id_producto' => $id_producto,
							'stock' => $cant_total,
							'costo_unitario'=>round(($precio_compra),2),
							'precio_unitario'=>round(($precio_compra),2),
							'update_date'=>$fecha_movimiento,
							// 'id_sucursal' => $id_sucursal
						);
						$where_clause="WHERE id_producto='$id_producto' AND caracteristicas= '$detalle'";
						$insert_stock = $this->utils->update($table2,$form_data2, $where_clause );
					}
          // echo _error();
          if(!$insert_stock)
          {
            $k = 0;
          }
        } // FOREACH
    	}//if $cuantos>0
			if($insert_mov && $j && $k)
			{
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Carga de inventario realizada con exito!";
			}
			else
			{
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Fallo al ingresar el inventario!";
			  $data['datos'] = $j." ".$k." ".$insert_mov;
			}
			echo json_encode($data);
		}
	}
	function salida()
	{
		if($this->input->method(TRUE) == "POST")
		{
			// $id_producto = $this->input->post("id_producto");
			$cuantos = $this->input->post('cuantos');
	    $fecha_movimiento= Y_m_d($this->input->post('fecha_movimiento'));
	    $total_compras = $this->input->post('total_compras');
	    $array_json=$this->input->post('json_arr');

			if ($cuantos>0)
			{
        $this->utils->begin();
        $hora=date("H:i:s");
        $fecha_ing=date('Y-m-d');


        $table_y='movimiento_producto';
        $form_data = array(
          'concepto' => "DESCARGA DE INVENTARIO",
          'total' => $total_compras,
          'tipo' => 'SALIDA',
          'proceso' => 'DES',
          //'referencia' => $numero_doc,
          'fecha' => $fecha_movimiento,
          'hora' => $hora,
          //'id_proveedor' => $id_proveedor,
          //'id_compra' => $id_fact,
        );
        $insert_mov = $this->utils->insert($table_y,$form_data);
        $id_movimiento=$this->utils->insert_id();

        $j = 1 ;
        $k = 1 ;
        $l = 1 ;
        $m = 1 ;
        $d = 1 ;
        $b = 1 ;

        $array = json_decode($array_json, true);
        foreach ($array as $fila)
				{
          $id_producto=$fila['id_producto'];
          $cantidad=$fila['cantidad'];
          $precio_compra=$fila['precio_compra'];
          $talla=$fila['talla'];
          // $precio_venta = $fila["precio_venta"];
          $suu = round($cantidad * $precio_compra, 3);
          $id_su="";
          /*cantidad de una presentacion por la unidades que tiene*/


          $cantidad=$cantidad;


          // $sql2="SELECT existencia FROM stock WHERE id_producto='$id_producto'";
          // $stock2=_query($sql2);
          // $row2=_fetch_array($stock2);
          // $nrow2=_num_rows($stock2);
					if($talla > 0)
					{
						$rowx = $this->inventario->get_stock_talla($talla);
						$existencias = $rowx->cantidad;
					}
					else {
						$rowx = $this->inventario->get_row_info($id_producto);
						$existencias = $rowx->stock;
					}

          $table1= 'movimiento_producto_detalle';
          $cant_total=$existencias-$cantidad;
          $form_data1 = array(
            'id_movimiento'=>$id_movimiento,
            'id_producto' => $id_producto,
            'id_presentacion' => $talla,
            'cantidad' => $cantidad,
            'costo' => $precio_compra,
            'precio' => $precio_compra,
            'stock_anterior'=>$existencias,
            'stock_actual'=>$cant_total,
            //'id_presentacion' => $id_presentacion,
          );
          $insert_mov_det = $this->utils->insert($table1,$form_data1);
          if(!$insert_mov_det)
          {
            $j = 0;
          }
					if($talla > 0)
					{
						$table2= 'producto_talla';
						$cant_total=$existencias - $cantidad;
						$form_data2 = array(
							'cantidad' => $cant_total,
						);
						$where2 = " id_talla='".$talla."'";
						$insert_stock = $this->utils->update($table2,$form_data2, $where2);
					}
					else
					{
	          $table2= 'producto';

	          $cant_total=$existencias-$cantidad;
	          $form_data2 = array(
	            'stock' => $cant_total,
	          );
	          $where = " id_producto='".$id_producto."'";
	          $insert_stock = $this->utils->update($table2,$form_data2, $where);
					}
          // echo _error();
          if(!$insert_stock)
          {
            $k = 0;
          }
        } // FOREACH
    	}//if $cuantos>0
			if($insert_mov && $j && $k)
			{
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Descargo realizado con exito!";
			}
			else
			{
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Fallo al realizar el descargo!";
			  $data['datos'] = $j." ".$k." ".$insert_mov;
			}
			echo json_encode($data);
		}
	}

	function ajuste_inventario()
	{
		if($this->input->method(TRUE) == "POST")
		{
			// $id_producto = $this->input->post("id_producto");

			$cuantos = $this->input->post('cuantos');
	    $fecha_movimiento= Y_m_d($this->input->post('fecha_movimiento'));
	    $total_compras = $this->input->post('total_compras');
	    $array_json=$this->input->post('json_arr');

			if ($cuantos>0)
			{
        $this->utils->begin();
        $hora=date("H:i:s");
        $fecha_ing=date('Y-m-d');


        $table_y='movimiento_producto';
        $form_data = array(
          'concepto' => "AJUSTE DE INVENTARIO",
          'total' => $total_compras,
          'tipo' => 'AJUSTE',
          'proceso' => 'AJ',
          //'referencia' => $numero_doc,
          'fecha' => $fecha_movimiento,
          'hora' => $hora,
          //'id_proveedor' => $id_proveedor,
          //'id_compra' => $id_fact,
        );
        $insert_mov = $this->utils->insert($table_y,$form_data);
        $id_movimiento=$this->utils->insert_id();

        $j = 1 ;
        $k = 1 ;
        $l = 1 ;
        $m = 1 ;
        $d = 1 ;
        $b = 1 ;

        $array = json_decode($array_json, true);
        foreach ($array as $fila)
				{
          $id_producto=$fila['id_producto'];
          $cantidad=$fila['cantidad'];
          $precio_compra=$fila['precio_compra'];
          $talla=$fila['talla'];
          // $precio_venta = $fila["precio_venta"];
          $suu = round($cantidad * $precio_compra, 3);
          $id_su="";
          /*cantidad de una presentacion por la unidades que tiene*/


          $cantidad=$cantidad;


          // $sql2="SELECT existencia FROM stock WHERE id_producto='$id_producto'";
          // $stock2=_query($sql2);
          // $row2=_fetch_array($stock2);
          // $nrow2=_num_rows($stock2);

					if($talla > 0)
					{
						$rowx = $this->inventario->get_stock_talla($talla);
						$existencias = $rowx->cantidad;
					}
					else {
						$rowx = $this->inventario->get_row_info($id_producto);
						$existencias = $rowx->stock;
					}

          $table1= 'movimiento_producto_detalle';
          $cant_total=$cantidad;
          $form_data1 = array(
            'id_movimiento'=>$id_movimiento,
            'id_producto' => $id_producto,
            'id_presentacion' => $talla,
            'cantidad' => $cantidad,
            'costo' => $precio_compra,
            'precio' => $precio_compra,
            'stock_anterior'=>$existencias,
            'stock_actual'=>$cant_total,
            //'id_presentacion' => $id_presentacion,
          );
          $insert_mov_det = $this->utils->insert($table1,$form_data1);
          if(!$insert_mov_det)
          {
            $j = 0;
          }
					if($talla > 0)
					{
						$table2= 'producto_talla';
						$cant_total=$cantidad;
						$form_data2 = array(
							'cantidad' => $cant_total,
						);
						$where2 = " id_talla='".$talla."'";
						$insert_stock = $this->utils->update($table2,$form_data2, $where2);
					}
					else
					{
	          $table2= 'producto';

	          $cant_total=$cantidad;
	          $form_data2 = array(
	            'stock' => $cant_total,
	            'precio' => $precio_compra,
	          );
	          $where = " id_producto='".$id_producto."'";
	          $insert_stock = $this->utils->update($table2,$form_data2, $where);
					}
          // echo _error();
          if(!$insert_stock)
          {
            $k = 0;
          }
        } // FOREACH
    	}//if $cuantos>0
			if($insert_mov && $j && $k)
			{
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Ajuste de inventario realizada con exito!";
			}
			else
			{
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Fallo al ajustar el inventario!";
			  $data['datos'] = $j." ".$k." ".$insert_mov;
			}
			echo json_encode($data);
		}
	}

	function total_texto()
	{
		if($this->input->method(TRUE) == "POST")
		{
	    $total=$this->input->post('total');
	    if ($total > 0)
	    {
	      list($entero, $decimal)=explode('.', $total);
				if($entero == "0")
				{
					$enteros_txt = "Cero";
				}
				else
				{
					$enteros_txt=num2letras($entero);
				}

	      if ($entero>1) {
	          $dolar=" dolares";
	      } else {
	          $dolar=" dolar";
	      }
	      $cadena_salida= "<h3 class='text-danger'>Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>&nbsp;&nbsp;</h3>";
	      echo $cadena_salida;
	    }
		}
	}

	function createcsv(){
		// Los datos
		$data["contenido"]= $this->inventario->lista();
		// Cargamos la vista preparada con los headers CSV
		$this->load->view('admin/inventario/lista_productos', $data);
	}

	function cargar_csv()
	{
		if($this->input->method(TRUE) == "POST")
		{
			$this->utils->begin();
			$upload_path = "assets/csv/";
			// $upload_path_thumb = "assets/img/productos/thumb/";

			$path = "csv/";
			// $path_thumb = "img/productos/thumb/";

			if ($_FILES["archivo_csv"]["name"] != "") {
				$archivo_csv = upload_image("archivo_csv",$upload_path);
				// resize_image($imagen, $upload_path,1000,1000,100,0,"");
				// resize_image($imagen, $upload_path,300,300,80,1,$upload_path_thumb);
				$lista=$path.$archivo_csv;
				// echo $lista;
				// $up_img1_thumb=$path_thumb.$imagen;
			}
			// echo $lista;

			$fp = fopen ("assets/".$lista,"r");
			if(!$fp)
			{
				// $this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Fallo al abrir el archivo!";
				// $data['datos'] = $j." ".$k." ".$insert_mov;
			}
			else
			{
				$hora=date("H:i:s");
				$fecha_ing=date('Y-m-d');
				$table_y='movimiento_producto';
				$form_data = array(
					'concepto' => "CARGA DE INVENTARIO MEDIANTE ARCHIVO",
					// 'total' => $total_compras,
					'tipo' => 'ENTRADA',
					'proceso' => 'II',
					'fecha' => $fecha_ing,
					'hora' => $hora,
				);

				$insert_mov = $this->utils->insert($table_y,$form_data);
				$id_movimiento=$this->utils->insert_id();

				$x = 0;
				$n = 0;
				$j = 1;
        $k = 1;
				while ($data = fgetcsv ($fp, 0, ",",'"')) {
					// $num = count ($data);
					list($id_producto, $descripcion, $precio, $stock, $cantidad) = $data;
					// echo $id_producto." <-> ".$descripcion." <-> ".$precio." <-> ".$stock." <-> ".$cantidad."\n";
					if($n > 0)
					{
						if($cantidad != 0 || $cantidad != "")
						{
							$rowx = $this->inventario->get_row_info($id_producto);
							$existencias = $rowx->stock;
							$table1= 'movimiento_producto_detalle';
							$cant_total=$cantidad + $existencias;
							$form_data1 = array(
								'id_movimiento'=>$id_movimiento,
								'id_producto' => $id_producto,
								'cantidad' => $cantidad,
								'costo' => $precio,
								'precio' => $precio,
								'stock_anterior'=>$existencias,
								'stock_actual'=>$cant_total,
								//'id_presentacion' => $id_presentacion,
							);
							$insert_mov_det = $this->utils->insert($table1,$form_data1);
							if(!$insert_mov_det)
							{
								$j = 0;
							}

							$table2= 'producto';

							$cant_total=$cantidad+$existencias;
							$form_data2 = array(
								'precio' => $precio,
								'stock' => $cant_total,
							);
							$where = " id_producto='".$id_producto."'";
							$insert_stock = $this->utils->update($table2,$form_data2, $where);
							// echo _error();
							if(!$insert_stock)
							{
								$k = 0;
							}
							$x += 1;
						}
					}
					$n += 1;
				}
				if($insert_mov && $j && $k && $x > 0)
				{
					$this->utils->commit();
					$data["type"] = "success";
					$data["title"] = "Información";
					$data["msg"] = "Carga de inventario realizada con exito!";
					$data['x'] = $x;
				}
				else
				{
					if($x == 0)
					{
						$xt = "Verifique que almenos un producto contenga una cantidad, de lo contrario el archivo no se prodra cargar";
					}
					else
					{
						$xt = "Fallo al realizar el ingreso de inventario!";
					}
					$this->utils->rollback();
					$data["type"] = "Error";
					$data["title"] = "Alerta!";
					$data["msg"] = $xt;
				  $data['datos'] = $j." ".$k." ".$insert_mov;
				  $data['x'] = $x;
				}
				fclose ($fp);
				// unlink("assets/".$lista);
			}
			echo json_encode($data);
		}
	}

}

/* End of file Inventario.php */
