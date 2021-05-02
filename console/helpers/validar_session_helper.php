<?php
if(!function_exists("validar_session"))
{
	function validar_session($obj)
	{
		if ($obj->session->admin == "") { redirect('admin/login', 'refresh'); }
	}
	function validate_profile($obj)
	{
		if (isset($obj->session->admin) || !isset($obj->session->logged_in)) {
			redirect('home', 'refresh');
		}
	}
	function validar_session_admin($obj)
	{
		if (!isset($obj->session->logged_in_admin)) {
			redirect('admin/login', 'refresh');
		}
	}
}
?>
