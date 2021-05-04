<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ErrorPage extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		validar_session($this);
		$this->load->helper('template_admin_helper');
	}
	public function index()
	{
		layout("admin/404");
	}

}

/* End of file Controllername.php */
