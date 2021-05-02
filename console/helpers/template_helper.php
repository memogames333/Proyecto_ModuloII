<?php
if (!function_exists('template')) {
	function template($view, $view_data = array(),$extras = array()) {
		$ci =& get_instance();
		$ci->load->model("MenuModel","menu");

		$id_usuario = $ci->session->id_usuario;
		$nombre = $ci->session->nombre;
		$apellido = $ci->session->apellido;
		$telefono = $ci->session->telefono;
		$logged = $ci->session->logged_in;

		$cats = $ci->menu->get_cats();
		$conf = $ci->menu->get_settings();
		$confa = $ci->menu->get_sucursales();

		$menu_data = array(
			'id_usuario' => $id_usuario,
			'nombre' => $nombre,
			'apellido' => $apellido,
			'telefono'=>$telefono,
			'logged'=>$logged,
			'cats'=>$cats,
			'conf'=>$conf,
		);

		$footer = array(
			"conf"=>$conf,
			"confa" => $confa
		);
		$ci->load->view('layout/header',$extras);
		$ci->load->view('layout/head',$menu_data);
		$ci->load->view($view, $view_data);
		$ci->load->view('layout/footer',$footer);
		$ci->load->view('layout/scripts',$extras);
		return true;
	}
}
