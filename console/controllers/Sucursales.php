<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sucursales extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("MenuModel","menu");
	}

	public function index()
	{
		$view_data = array(
			"row"=>$this->menu->get_sucursales(),
		);
		template("sucursales",$view_data,"");
	}

}

/* End of file Sucursales.php */
