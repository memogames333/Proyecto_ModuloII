<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("MenuModel","menu");
		$this->load->helper("utilities_helper");
	}

	public function index()
	{
		if(isset($this->session->yaimg))
		{
			$this->session->yaimg = 1;
		}
		else {
			$this->session->yaimg = 0;
		}

		$detalle=$this->menu->detalles();
		$confs=$this->menu->get_settings();
		$cats = $this->menu->get_cats_home();
		$view_data = array(
			"cats"=>$cats,
			"rows"=>$this->menu->get_productos_destacados(),
			"detalle"=>$detalle,
			"confs"=>$confs,
			"ya"=>$this->session->yaimg,
		);
		$extras = array(
			"css"=>array(
				'css/slider_resposive.css',
				"libs/blueimp/css/blueimp-gallery.min.css",
			),
			"js"=>array(
				"js/funciones/home.js",
				"libs/blueimp/js/blueimp-gallery.min.js",
			)
		);
		template("home",$view_data,$extras);
	}

}

/* End of file Home.php */
