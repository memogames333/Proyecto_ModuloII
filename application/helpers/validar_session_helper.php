<?php
if(!function_exists("validar_session"))
{
	function validar_session($obj)
	{
		if ($obj->session->logged_in == "") {
			redirect('login', 'refresh');
		}
	}
}

if(!function_exists("permissions_user"))
{
	function permissions_user($obj,$filename="")
	{
		if ($obj->session->logged_in == "") {
			redirect('login', 'refresh');
		}
		else{
			$ci =& get_instance();
			$ci->load->model('Menu_model','menu');
			$id = $obj->session->id_usuario;
			$permissions = $ci->menu->permissions_user($id,$filename);
			if($obj->session->admin==0){
				if($permissions==FALSE) redirect('errorpage', 'refresh');
			}
		}

	}
}
