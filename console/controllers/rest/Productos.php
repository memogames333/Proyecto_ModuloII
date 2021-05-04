<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends yidas\rest\Controller {
	/*
	Global table name
	*/

	function __construct()
	{
		parent::__construct();
		$this->load->model("api/ProductosModel","productos");
	}
	/************************************************************/
	/************************************************************/
	/***************************API******************************/
	/************************************************************/
	/************************************************************/

	public function mostrar()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$limit = 10;
		$categoriaID = ($this->uri->segment(4));
		$page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 1;
		if(is_numeric($categoriaID))
		{
			$page_sql = ($page-1)*$limit;
			$productos = $this->productos->getByCategoria($categoriaID,$limit,$page_sql);
			if($productos != null)
			{
				$pages = ceil($this->productos->totalByCategoria($categoriaID)/$limit);
				$datos = array('pages'=> $pages, 'actual' => intval($page), 'data'=>$productos);
				$data = $this->pack($datos, OK, "Productos");
			}
			else {
				$data = $this->pack(null,NOT_FOUND,'Productos');
			}
			return $this->response->json($data, OK);
		}
		else
		{
			return $this->response->json($this->pack("Invalid argument",OK));
		}
	}
	public function buscar()
	{
		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}
		$limit = 10;
		$keyword = ($this->uri->segment(4));
		$keyword = str_replace("-"," ",$keyword);
		$page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 1;
		if($keyword != "")
		{
			$page_sql = ($page-1)*$limit;
			$productos = $this->productos->getByKeyWord($keyword,$limit,$page_sql);
			if($productos != null)
			{
				$pages = ceil($this->productos->totalByKeyword($keyword)/$limit);
				$datos = array('pages'=> $pages, 'actual' => intval($page), 'data'=>$productos);
				$data = $this->pack($datos, OK, "Productos");
			}
			else {
				$data = $this->pack(null,NOT_FOUND,'Productos');
			}
			return $this->response->json($data, OK);
		}
		else
		{
			return $this->response->json($this->pack("Invalid argument",OK));
		}
	}
	protected function show($resourceID = -1)
	{

		$result = Authorization::verifyToken();

		if($result['hasError']){
			return $this->response->json($result['data'], $result['code']);
		}

		if (is_numeric($resourceID)){
			$producto = $this->productos->get($resourceID);
			if($producto != null)
			{
				$data = $this->pack($producto, OK, "Producto");
			}
			else {
				$data = $this->pack(null, NOT_FOUND, "Producto");
			}
		}
		else
		{
			return $this->response->json($this->pack("Invalid argument",BAD_REQUEST));
		}
		return $this->response->json($data, OK);

	}
	/************************************************************/
	/************************************************************/
	/***************************API******************************/
	/************************************************************/
	/************************************************************/


}

/* End of file Productos.php */
