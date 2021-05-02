<?php

if (!function_exists('layout')) {
	function layout($view, $view_data = array(), $extras = array()) {
		$ci =& get_instance();
		validar_session_admin($ci);
		$ci->load->model('admin/Menu_model');
		$nombre_session = $ci->session->usuario;
		$id_usuario = $ci->session->id_usuario;
		$admin = $ci->session->admin;
		$menus = $ci->Menu_model->get_menu($id_usuario,$admin);
		$modulos_base = $ci->Menu_model->get_controller($id_usuario,$admin);
		$conf = $ci->Menu_model->get_config();
		$modulos = array();
		if($menus!=false && $modulos_base!=false){
			foreach ($menus as $menu)
			{
				$id_menu = $menu->id_menu;
				$modulos[$id_menu] = array_filter($modulos_base, function($modulo) use ($id_menu)
				{
					return $modulo->id_menu == $id_menu;
				});
			}
			$menu_data = array(
				'menus'=>$menus,
				'modulos' => $modulos,
				'nombre_session' => $nombre_session,
				'logo'=> $conf->logo_empresa,
			);
		}else{
			$menu_data = array(
				'nombre_session' => $nombre_session,
				'logo'=> $conf->logo_empresa,
			);
		}

		$ci->load->view('admin/template/header',$extras);
		$ci->load->view('admin/template/menu',$menu_data);
		$ci->load->view($view, $view_data);
		$ci->load->view('admin/template/footer');
		return true;
	}
}

?>
