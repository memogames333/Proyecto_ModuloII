<?php

if (!function_exists('layout')) {
	function layout($view, $view_data = array(), $extras = array()) {
		$ci =& get_instance();
		// INFO PARA EL HEAD
		// $logo = "assets/images/logo.png";
		// $telefono = "+503 2613 7470";
		// $data_head = array(
		// 	'logo' => $logo,
		// 	'telefono' => $telefono
		// );
		$clave = $view_data["clave"];

		$ci->load->view('layout/header');
		$ci->load->view('layout/head');
		// if($clave != 1)
		// {
		// 	$ci->load->view('layout/slider');
		// }
		$ci->load->view('layout/menu', $view_data);
		//$ci->load->view('layout/categorias',$data_categorias);
		$ci->load->view($view, $view_data);
		$ci->load->view('layout/footer');
		//$ci->load->view('layout/scripts',$extras);
		return true;
	}
}

if (!function_exists('layout1')) {
	function layout1($view, $view_data, $extras = array()) {
		$ci =& get_instance();
		// INFO PARA EL HEAD
		$logo = "assets/images/logo.png";
		$telefono = "+503 2613 7470";
		$data_head = array(
			'logo' => $logo,
			'telefono' => $telefono
		);

		// INFO PARA EL MENU
		$categorias = array(
			array("id_categoria"=>1,"nombre"=>"Computadoras"),
			array("id_categoria"=>2,"nombre"=>"Monitores"),
		);
		$categoriasub = array(
			array("id_categoria"=>1,"nombre"=>"Computadoras", "subcategorias" => array(array("id_sub"=>1,"nombre"=>"Desktop"),array("id_sub"=>2,"nombre"=>"Laptop"))),
			array("id_categoria"=>2,"nombre"=>"Monitores", "subcategorias" => array(array("id_sub"=>1,"nombre"=>"Nuevos"),array("id_sub"=>2,"nombre"=>"Usados"))),
		);
		$top3 = array(
			array("img"=>base_url()."assets/images/mega-img-1.jpg","descripcion"=>"Top1", "precio" => "$20.00"),
			array("img"=>base_url()."assets/images/mega-img-2.jpg","descripcion"=>"Top2", "precio" => "$10.00"),
			array("img"=>base_url()."assets/images/mega-img-3.jpg","descripcion"=>"Top3", "precio" => "$5.00"),
		);
		$descuentos = array(
			array("img"=>base_url()."assets/images/mega-1.jpg","porcentaje"=>"40%"),
			array("img"=>base_url()."assets/images/mega-2.jpg","porcentaje"=>"60%"),
		);
		$destacados = array(
			array("img"=>base_url()."assets/images/mega-b-1.jpg","nombre"=>"Camara", "url" => "camara/1"),
			array("img"=>base_url()."assets/images/mega-b-2.jpg","nombre"=>"Mouse", "url" => "mouse/1"),
			array("img"=>base_url()."assets/images/mega-b-3.jpg","nombre"=>"Audifonos", "url" => "audifono/1"),
			array("img"=>base_url()."assets/images/mega-b-4.jpg","nombre"=>"Speaker", "url" => "speaker/1"),
		);
		$data_menu = array(
			'categorias' => $categorias,
			'categoriasub' => $categoriasub,
			'top3' => $top3,
			'descuentos' => $descuentos,
			'destacados' => $destacados,
		);

		// INFO PARA CATEGORIAS
		$categoriaslfet = array(
			array("id_categoria"=>1,"nombre"=>"Computadoras", "subcategorias" => array(array("id_sub"=>1,"nombre"=>"Desktop"),array("id_sub"=>2,"nombre"=>"Laptop"))),
			array("id_categoria"=>2,"nombre"=>"Monitores", "subcategorias" => array(array("id_sub"=>1,"nombre"=>"Nuevos"),array("id_sub"=>2,"nombre"=>"Usados"))),
			array("id_categoria"=>3,"nombre"=>"Otra", "subcategorias" => null),
		);
		$data_categorias = array(
			'categoriasleft' => $categoriaslfet,
		);

		// INFO PARA SLIDER
		$sliders = array(
			array("img"=>base_url()."assets/images/girl-1.png","titulo"=>"Camara", "descripcion1"=>"Camara", "descripcion2"=>"Camara", "enlace" => "camara/1", "texto_enlace"=>"Camara"),
			array("img"=>base_url()."assets/images/girl-2.png","titulo"=>"Camara", "descripcion1"=>"Camara", "descripcion2"=>"Camara", "enlace" => "camara/1", "texto_enlace"=>"Camara"),
		);
		$minisliders = array(
			array("img"=>base_url()."assets/images/sb-1.png", "enlace" => "camara/1"),
			array("img"=>base_url()."assets/images/sb-2.png", "enlace" => "camara/1"),
			array("img"=>base_url()."assets/images/sb-3.png", "enlace" => "camara/1"),
		);
		$data_sliders = array(
			'sliders' => $sliders,
			'minisliders' => $minisliders,
		);
		// INFO PARA FOOTER
		$correo = "info@tudigitalmarket.com";
		$direccion = "4A AVE. SUR #402 BIS, BARRIO EL CALVARIO, SAN MIGUEL, EL SALVADOR";
		$data_foot = array(
			'telefono' => $telefono,
			'correo' => $correo,
			'direccion' => $direccion,
		);

		$combi = array_merge($data_foot,$extras);
		$ci->load->view('layout/header');
		$ci->load->view('layout/head',$data_head);
		$ci->load->view('layout/menu',$data_menu);
		$ci->load->view($view, $view_data);
		$ci->load->view('layout/footer',$combi);
		//$ci->load->view('layout/scripts',$extras);
		return true;
	}
}

?>
