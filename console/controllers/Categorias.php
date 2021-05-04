<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("MenuModel","menu");
		$this->load->model("Categorias_model","categorias");
	}

	public function index()
	{
		$cats = $this->menu->get_cats();
		$cats_completa = array();
		foreach ($cats as $cat)
		{
			$id = $cat->id_categoria;
			$cat->issub = $this->categorias->issubs($id);
			array_push($cats_completa,$cat);
		}
		$data = array(
			"cats"=>$cats,
		);
		$extras = array(
			'css' => array(),
			'js' => array(),
		);
		template("categorias/categorias",$data,$extras);
	}

}

/* End of file Controllername.php */
