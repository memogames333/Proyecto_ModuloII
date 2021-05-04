<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends yidas\rest\Controller {

	/*
	Global table name
	*/

	function __construct()
	{
		parent::__construct();
		$this->load->Model("api/CategoriasModel","categorias");
	}
	/************************************************************/
	/************************************************************/
	/***************************API******************************/
	/************************************************************/
	/************************************************************/
	public function show($resourceID = 1)
	{
		$result = Authorization::verifyToken();

		if ($result['hasError']) {
			return $this->response->json($result['data'], $result['code']);
		}
		$limit = 10;
		$page = $resourceID;

		$page_sql = ($page-1)*$limit;
		$result = $this->categorias->collection($limit,$page_sql);
		if($result != null)
		{
			$pages = ceil($this->categorias->total_rows()/$limit);

			$datos = array('pages'=> $pages, 'actual' => intval($page), 'data'=>$result);

			$res = $this->pack($datos, OK, "Categorias");
		}
		else
		{
			$res = $this->pack(null, NOT_FOUND, "Categorias");
		}

		return $this->response->json($res, OK);
	}
	/************************************************************/
	/************************************************************/
	/***************************API******************************/
	/************************************************************/
	/************************************************************/
}

/* End of file Categorias.php */
