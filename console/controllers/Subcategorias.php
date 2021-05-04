<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subcategorias extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("MenuModel","menu");
		$this->load->model("Categorias_model","categorias");
	}

	public function index($cat)
	{
		$id_cate = 0;
		if ($cat != "") {
			$id_cate = $this->categorias->getIdCat($cat);
		}
		$cats = $this->categorias->getSubcategorias($id_cate);
		$data = array(
			"cats"=>$cats,
			"param"=>$cat,
		);
		$extras = array(
			'css' => array(),
			'js' => array(),
		);
		template("categorias/subcategorias",$data,$extras);
	}

}

/* End of file Controllername.php */
