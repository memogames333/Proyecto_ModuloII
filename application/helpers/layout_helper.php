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

		$ci->load->view('layout/header');
		$ci->load->view('layout/head');
		// if($clave != 1)
		// {
		// 	$ci->load->view('layout/slider');
		// }
		//$ci->load->view('layout/categorias',$data_categorias);
		$ci->load->view($view, $view_data);
		$ci->load->view('layout/footer');
		//$ci->load->view('layout/scripts',$extras);
		return true;
	}
}

?>
