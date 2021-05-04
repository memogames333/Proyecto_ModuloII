<?php

class Catalogo extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		// Load pagination library
		$this->load->library('ajax_pagination');
		//load post model
		$this->load->model('Catalogo_model');
		$this->load->model('Categorias_model');
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
		//per page limit
		$this->perPage = 30;
	}

	public function index()
	{
		$this->cat($cate = "");
	}

	function getIdByCat($cate)
	{
		$id_cate = 0;
		if ($cate != "") {
			$id_cate = $this->Categorias_model->getIdCat($cate);
		}

		$this->mostrar($id_cate, $cate);

	}
	function getIdBySubCat($cate)
	{
		$id_cate = 0;
		if ($cate != "") {
			$id_cate = $this->Categorias_model->getIdSubCat($cate);
		}

		$this->mostrar_sub($id_cate, $cate);

	}

	function cat($cate = "")
	{

		$this->getIdByCat($cate);
	}
	function subcat($cate = "")
	{

		$this->getIdBySubCat($cate);
	}
	function search($keywords= "")
	{
		//echo "Busqueda:".$keywords;
		$data = array();
		$page = 0;
		if (!$page) {
			$offset = 0;
		} else {
			$offset = 1 + $page;
		}
		// Get record count
		$conditions['returnType'] = 'count';
		if (!empty($keywords)) {
			$conditions['search']['keywords'] = $keywords;
		}
		$totalRec = $this->Catalogo_model->getRows($conditions);

		// Pagination configuration
		$config['target'] = '#dataList';
		$config['base_url'] = base_url('catalogo/ajaxPaginationData');
		$config['total_rows'] = $totalRec;
		$config['per_page'] = $this->perPage;
		$config['link_func'] = 'searchFilter';

		//styling
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_link'] = 'Sig.';
		$config['prev_link'] = 'Ant.';
		$config['next_tag_open'] = '<li class="pg-next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="pg-prev">';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';


		// Initialize pagination library
		$this->ajax_pagination->initialize($config);

		// Get records
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		unset($conditions['returnType']);

		// unset($conditions['returnType']);
		//  $conditions['returnType'];
		$data['id_cat_activo'] = array(
			'id_cat_activo' => ""
		);
		$data['productos'] = $this->Catalogo_model->getRows($conditions);
		$data['numrows'] = $totalRec;
		$catea = $this->Categorias_model->getRows();
		$cate_o = array();
		foreach ($catea as $kt)
		{
			$id = $kt->id_categoria;
			$kt->issub = $this->Categorias_model->issubs($id);
			array_push($cate_o,$kt);
		}
		$data['categorias'] = $cate_o;

		// Load the list page view
		$extras = array(
			'css' => array(
				0 => "css/plugins/sweetalert.css"
			),
			'js' => array(
				0 => "js/sweetalert.min.js",
				1 => "js/funciones/catalogo.js"
			),
		);
		template("catalogo/catalog", $data, $extras);
	}
	function mostrar($id_cate = "", $cate = "")
	{

		$data = array();
		$page = 0;
		if (!$page) {
			$offset = 0;
		} else {
			$offset = 1 + $page;
		}
		// Get record count
		$conditions['returnType'] = 'count';
		if ($id_cate > 0) {
			$conditions['search']['byCat'] = $id_cate;
		}
		$totalRec = $this->Catalogo_model->getRows($conditions);

		// Pagination configuration
		$config['target'] = '#dataList';
		$config['base_url'] = base_url('catalogo/ajaxPaginationData');
		$config['total_rows'] = $totalRec;
		$config['per_page'] = $this->perPage;
		$config['link_func'] = 'searchFilter';

		//styling
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_link'] = 'Sig.';
		$config['prev_link'] = 'Ant.';
		$config['next_tag_open'] = '<li class="pg-next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="pg-prev">';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';


		// Initialize pagination library
		$this->ajax_pagination->initialize($config);

		// Get records
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		unset($conditions['returnType']);

		// unset($conditions['returnType']);
		//  $conditions['returnType'];
		$data['id_cat_activo'] = array(
			'id_cat_activo' => $id_cate
		);
		$data['productos'] = $this->Catalogo_model->getRows($conditions);
		$data['numrows'] = $this->Catalogo_model->numRowsbyCat($id_cate);
		$catea = $this->Categorias_model->getRows();
		$cate_o = array();
		foreach ($catea as $kt)
		{
			$id = $kt["id_categoria"];
			$kt["issub"] = $this->Categorias_model->issubs($id);
			array_push($cate_o,$kt);
		}
		$data['categorias'] = $cate_o;

		// Load the list page view
		$extras = array(
			'css' => array(
				0 => "css/plugins/sweetalert.css"
			),
			'js' => array(
				0 => "js/sweetalert.min.js",
				1 => "js/funciones/catalogo.js"
			),
		);
		template("catalogo/catalog", $data, $extras);
	}
	function mostrar_sub($id_cate = "", $cate = "")
	{

		$data = array();
		$page = 0;
		if (!$page) {
			$offset = 0;
		} else {
			$offset = 1 + $page;
		}
		// Get record count
		$conditions['returnType'] = 'count';
		if ($id_cate > 0) {
			$conditions['search']['byCat'] = $id_cate;
		}
		$totalRec = $this->Catalogo_model->getRowsSub($conditions);

		// Pagination configuration
		$config['target'] = '#dataList';
		$config['base_url'] = base_url('catalogo/ajaxPaginationDataSub');
		$config['total_rows'] = $totalRec;
		$config['per_page'] = $this->perPage;
		$config['link_func'] = 'searchFilter';

		//styling
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_link'] = 'Sig.';
		$config['prev_link'] = 'Ant.';
		$config['next_tag_open'] = '<li class="pg-next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="pg-prev">';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';


		// Initialize pagination library
		$this->ajax_pagination->initialize($config);

		// Get records
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		unset($conditions['returnType']);

		// unset($conditions['returnType']);
		//  $conditions['returnType'];
		$data['id_cat_activo'] = array(
			'id_cat_activo' => $id_cate
		);
		$data['productos'] = $this->Catalogo_model->getRowsSub($conditions);
		$data['numrows'] = $this->Catalogo_model->numRowsbySubCat($id_cate);
		$data['categorias'] = $this->Categorias_model->getRowsSub($id_cate);
		$data['cate'] = $this->Categorias_model->nombre_cat($id_cate);

		// Load the list page view
		$extras = array(
			'css' => array(
				0 => "css/plugins/sweetalert.css"
			),
			'js' => array(
				0 => "js/sweetalert.min.js",
				1 => "js/funciones/catalogo.js"
			),
		);
		template("catalogo/catalog_sub", $data, $extras);
	}

	function stock_talla()
	{
		// Define offset
		$id = $this->input->post('product_id');
		$talla = $this->input->post('talla');
		$existencia = $this->Catalogo_model->get_stock($talla);
		$data["stock"] = 0;
		$data["descripcion"] = "";
		if($existencia != null)
		{
			$data["descripcion"] = $existencia->talla;
			$data["stock"] = round($existencia->cantidad,0);
		}
		echo json_encode($data);
	}
	function getIdCat()
	{
		// Define offset
		$cat = $this->input->post('cat');
		if ($cat != "")
		$data['idCat'] = $this->Categorias_model->getIdCat($cat);
		else
		$data['idCat'] = 0;
		//$data['idCat']=random_int(1, 15);
		echo json_encode($data);
	}

	function getParamCat()
	{
		// Define offset
		$cat = $this->input->post('cat');
		if ($cat != "")
		$data['cat'] = $this->Categorias_model->getParamCat($cat);
		else
		$data['cat'] = "varios";
		//$data['idCat']=random_int(1, 15);
		echo json_encode($data);
	}
	function getParamSubCat()
	{
		// Define offset
		$cat = $this->input->post('cat');
		if ($cat != "")
		$data['cat'] = $this->Categorias_model->getParamSubCat($cat);
		else
		$data['cat'] = "varios";
		//$data['idCat']=random_int(1, 15);
		echo json_encode($data);
	}

	function ajaxPaginationData()
	{
		// Define offset
		$page = $this->input->post('page');
		if (!$page) {
			$offset = 0;
		} else {
			$offset = $page;
		}

		// Set conditions for search and filter
		$keywords = $this->input->post('keywords');
		$sortBy = $this->input->post('sortBy');
		$byCat = $this->input->post('byCat');
		if (!empty($keywords)) {
			$conditions['search']['keywords'] = $keywords;
		}
		if (!empty($sortBy)) {
			$conditions['search']['sortBy'] = $sortBy;
		}
		if (!empty($byCat)) {
			$conditions['search']['byCat'] = $byCat;
		}
		// Get record count
		$conditions['returnType'] = 'count';
		$totalRec = $this->Catalogo_model->getRows($conditions);

		// Pagination configuration
		$config['target'] = '#dataList';
		$config['base_url'] = base_url('catalogo/ajaxPaginationData');
		$config['total_rows'] = $totalRec;
		$config['per_page'] = $this->perPage;
		$config['link_func'] = 'searchFilter';

		//styling
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_link'] = 'Sig.';
		$config['prev_link'] = 'Ant.';
		$config['next_tag_open'] = '<li class="pg-next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="pg-prev">';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		// Initialize pagination library
		$this->ajax_pagination->initialize($config);

		// Get records
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		unset($conditions['returnType']);

		$data['productos'] = $this->Catalogo_model->getRows($conditions);
		$data['numrows'] = $this->Catalogo_model->numRows();

		// Load the data list view
		$this->load->view('catalogo/catalogo-pagination-data', $data, false);

		//template("catalogo/catalogo-pagination-data', ",$data,"");
	}
	function ajaxPaginationDataSub()
	{
		// Define offset
		$page = $this->input->post('page');
		if (!$page) {
			$offset = 0;
		} else {
			$offset = $page;
		}

		// Set conditions for search and filter
		$keywords = $this->input->post('keywords');
		$sortBy = $this->input->post('sortBy');
		$byCat = $this->input->post('byCat');
		if (!empty($keywords)) {
			$conditions['search']['keywords'] = $keywords;
		}
		if (!empty($sortBy)) {
			$conditions['search']['sortBy'] = $sortBy;
		}
		if (!empty($byCat)) {
			$conditions['search']['byCat'] = $byCat;
		}
		// Get record count
		$conditions['returnType'] = 'count';
		$totalRec = $this->Catalogo_model->getRowsSub($conditions);

		// Pagination configuration
		$config['target'] = '#dataList';
		$config['base_url'] = base_url('catalogo/ajaxPaginationDataSub');
		$config['total_rows'] = $totalRec;
		$config['per_page'] = $this->perPage;
		$config['link_func'] = 'searchFilter';

		//styling
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_link'] = 'Sig.';
		$config['prev_link'] = 'Ant.';
		$config['next_tag_open'] = '<li class="pg-next">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="pg-prev">';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		// Initialize pagination library
		$this->ajax_pagination->initialize($config);

		// Get records
		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;
		unset($conditions['returnType']);

		$data['productos'] = $this->Catalogo_model->getRowsSub($conditions);
		$data['numrows'] = $this->Catalogo_model->numRows();

		// Load the data list view
		$this->load->view('catalogo/catalogo-pagination-data_sub', $data, false);

		//template("catalogo/catalogo-pagination-data', ",$data,"");
	}

	//agregar al carro
	function add_to_cart()
	{
		$id = $this->input->post('product_id')."_".$this->input->post('talla');
		$cart = $this->cart->contents();
		$exis = 0;
		$stock = 0;
		$stockable = 0;
		$incart = 0 ;
		$nuevo = $this->input->post('quantity');
		foreach ($cart as $item) {
			if($item["id"] == $id)
			{
				$exis = 1;
				list($ids, $talla) = explode("_",$item["id"]);
				$stockable = 0;
				if($talla > 0)
				{
					$stockable = 1;
					$existencia = $this->Catalogo_model->get_stock($talla);
				}
				else
				{
					if($this->Catalogo_model->is_stockeable($ids))
					{
						$stockable = 1;
						$existencia = $this->Catalogo_model->get_stock_producto($ids);
					}
					else {
						$existencia = null;
						$stockable = 0;
					}
				}
				if($existencia != null)
				{
					$stock = round($existencia->cantidad,0);
				}
				$incart = $item["qty"];
			}
		}
		if($exis)
		{
			if($stockable)
			{
				$disponible = $stock - $incart;
				if($disponible < $nuevo)
				{
					$nuevo = $disponible;
				}
			}
		}
		echo $stock."<br>";
		echo $stockable."<br>";
		echo $nuevo."<br>";
		echo $disponible."<br>";
			$data = array(
				'id' => $this->input->post('product_id')."_".$this->input->post('talla'),
				'name' => $this->input->post('product_name'),
				'add' => $this->input->post('product_name_add'),
				'talla' => $this->input->post('talla'),
				'price' => $this->input->post('product_price'),
				'qty' => $nuevo,
				'image' => $this->input->post('image'),
			);
			$this->cart->insert($data);

	}

	function get_image($id)
	{
		$data['img_product'] = $this->Catalogo_model->getImage($id);
	}

	function show_cart_page()
	{
		$data['listaprod'] = $this->cart->contents();
		$this->load->view('catalogo/cart', $data);

	}

	function cart()
	{
		$cart_completo = array();

		foreach ($this->cart->contents() as $items)
		{
			list($ids, $talla) = explode("_",$items["id"]);
			$stockable = 0;
			if($talla > 0)
			{
				$stockable = 1;
				$existencia = $this->Catalogo_model->get_stock($talla);
			}
			else
			{
				if($this->Catalogo_model->is_stockeable($ids))
				{
					$stockable = 1;
					$existencia = $this->Catalogo_model->get_stock_producto($ids);
				}
				else {
					$existencia = null;
					$stockable = 0;
				}
			}
			$stock =  0;
			if($existencia != null)
			{
				$stock = round($existencia->cantidad,0);
			}

			if($items["qty"] > $stock && $stockable)
			{
				$items["qty"] = $stock;
			}
			array_push($cart_completo,$items);
		}
		$data['listaprod'] = $cart_completo;

		$minimo = $this->Catalogo_model->getMinimo();
		$data['minimo'] = $minimo;
		$extras = array(
			"css"=>array(
				"css/responsive.css"
			),
			"js"=>array()
		);
		template("catalogo/cart", $data, $extras);
	}

	function show_cart()
	{
		/*$cart_completo = array();

		foreach ($this->cart->contents() as $items)
		{
			list($idp, $talla) = explode("_",$items["id"])[0];
			$stockable = 0;
			if($talla > 0)
			{
				$stockable = 1;
				$existencia = $this->Catalogo_model->get_stock($talla);
			}
			else
			{
				if($this->Catalogo_model->is_stockeable($ids))
				{
					$stockable = 1;
					$existencia = $this->Catalogo_model->get_stock_producto($ids);
				}
				else {
					$existencia = null;
					$stockable = 0;
				}
			}
			$stock =  0;
			if($existencia != null)
			{
				$stock = round($existencia->cantidad,0);
			}

			if($items["qty"] > $stock && $stockable)
			{
				$items["qty"] = $stock;
				$items["amount"] = $stock * $items["price"];
			}
			array_push($cart_completo,$items);
		}
		$data['listaprod'] = $cart_completo;*/

		$data['listaprod'] = $this->cart->contents();

		$this->load->view('catalogo/cart_table_reload', $data);

	}

	function load_cart_page()
	{
		echo $this->cart();

	}

	function load_cart()
	{
		echo $this->show_cart();
	}

	function delete_cart()
	{
		$data = array(
			'rowid' => $this->input->post('row_id'),
			'qty' => 0,
		);
		$this->cart->update($data);
		echo $this->show_cart();
	}

	function delete_all_cart()
	{
		$this->cart->destroy();
		echo $this->show_cart();
		//	echo $this->show_cart_total();
	}

	function update_cart()
	{
		// Recieve post values,calcute them and update
		$price = $this->input->post('price');
		$qty = $this->input->post('qty');
		$ids = $this->input->post('row_ids');
		$id = $this->input->post('row_id');
		$talla = $this->input->post('row_talla');
		$stockable = 0;
		if($talla > 0)
		{
			$stockable = 1;
			$existencia = $this->Catalogo_model->get_stock($talla);
		}
		else
		{
			if($this->Catalogo_model->is_stockeable($ids))
			{
				$stockable = 1;
				$existencia = $this->Catalogo_model->get_stock_producto($ids);
			}
			else {
				$existencia = null;
				$stockable = 0;
			}
		}
		$stock =  0;
		if($existencia != null)
		{
			$stock = round($existencia->cantidad,0);
		}
		if ($qty < 1)
		{
			$qty = 1;
		}
		else
		{
			if($qty > $stock && $stockable)
			{
				$qty = $stock;
			}
			$amount = $price * $qty;
			$data = array(
				'rowid' => $id,
				'qty' => $qty,
				'price' => $price,
				'amount' => $amount,
			);
			$this->cart->update($data);
		}
		echo $this->show_cart();
	}

	function update_cart_list()
	{
		$no = 0;
		$output = "";
		if ($this->cart->total_items() > 0) {
			$output .= '<li class="total bg-theme">
			<span class="foat-left"><strong>Total</strong>: $' . number_format($this->cart->total(), 2, '.', '') . '</span>
			<a href="' . base_url("catalogo/cart") . '" class="butn-style2 small white float-right w-auto"><span>Ver <i class="ti-shopping-cart"></i></span></a>
			</li>';
		}
		foreach ($this->cart->contents() as $items) {
			$no++;
			$add  = $items['add'];
			/*list($ids, $talla) = explode("_",$items["id"]);
			$stockable = 0;
			if($talla > 0)
			{
				$stockable = 1;
				$existencia = $this->Catalogo_model->get_stock($talla);
			}
			else
			{
				if($this->Catalogo_model->is_stockeable($ids))
				{
					$stockable = 1;
					$existencia = $this->Catalogo_model->get_stock_producto($ids);
				}
				else {
					$existencia = null;
					$stockable = 0;
				}
			}
			$stock =  0;
			if($existencia != null)
			{
				$stock = round($existencia->cantidad,0);
			}
			if ($qty < 1)
			{
				$qty = 1;
			}
			else
			{
				if($qty > $stock && $stockable)
				{
					$qty = $stock;
				}
			}
			$items["qty"] = $qty;
			$price = $items["price"];
			$amount = $qty * $price;
			//$items[""]*/
			$output .= '
			<li>
			<a href="'.base_url("catalogo/cart").'" class="photo"><img src="' . $items['image'] . '" class="cart-thumb" alt="..." /></a>
			<h6><a href="'.base_url("catalogo/cart").'">' . $items['name'].' '.$add.'</a></h6>
			<p>' . $items['qty'] . ' x - <span class="price">$' . number_format($items['price'], 2, '.', '') . '</span></p>
			</li>';
		}
		if ($no > 4) {
			$output .= '<li class="total bg-theme">
			<span class="foat-left"><strong>Total</strong>: $' . number_format($this->cart->total(), 2, '.', '') . '</span>
			<a href="' . base_url("catalogo/cart") . '" class="butn-style2 small white float-right w-auto"><span>Ver <i class="ti-shopping-cart"></i></span></a>
			</li>';
		}

		/*$data = array(
			'rowid' => $ids,
			'qty' => $qty,
			'price' => $price,
			'amount' => $amount,
		);*/
		$this->cart->update($data);
		echo $output;
	}

	function total_items_list()
	{
		$total = 0;
		$output = '' . $this->cart->total_items();
		echo $output;
	}

	public function add_wishlist(){
		$id_producto = $this->input->post("product_id");
		$id_usuario = $this->session->id_usuario;
		if(isset($id_usuario)){
			$existe = $this->Catalogo_model->compare_wish($id_producto,$id_usuario);
			if($existe==false){
				$form = array(
					"id_producto"=>$id_producto,
					"id_usuario"=>$id_usuario,
					"fecha"=>date("Y-m-d"),
					"hora"=>date("H:i:s"),
				);
				$this->utils->begin();
				$insert = $this->utils->insert("wishlist",$form);
				if($insert){
					$this->utils->commit();
					$xdatos["type"] = "success";
					$xdatos["title"] = "Exito";
					$xdatos["msg"] = "Producto agregado exitosamente";
				}
				else{
					$this->utils->rollback();
					$xdatos["type"] = "danger";
					$xdatos["title"] = "Error";
					$xdatos["msg"] = "El producto no se pudo agregar";
				}
			}else{
				$xdatos["type"] = "warning";
				$xdatos["title"] = "Alerta";
				$xdatos["msg"] = "El producto ya esta en tu lista de deseos";
			}
		}else{
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["msg"] = "Debes iniciar sesion primero!";
		}
		echo json_encode($xdatos);
	}

	function remove_wishlist(){
		$id_producto = $this->input->post("product_id");
		$id_usuario = $this->session->id_usuario;
		if(isset($id_usuario)){
			$id_wishlist = $this->Catalogo_model->get_wishlist_row($id_producto,$id_usuario);
			$this->utils->begin();
			$where = " id_wishlist='".$id_wishlist."'";
			$insert = $this->utils->delete("wishlist",$where);
			if($insert){
				$this->utils->commit();
				$xdatos["type"] = "success";
				$xdatos["title"] = "Exito";
				$xdatos["msg"] = "Producto eliminado exitosamente";
			}
			else{
				$this->utils->rollback();
				$xdatos["type"] = "danger";
				$xdatos["title"] = "Error";
				$xdatos["msg"] = "El producto no se pudo eliminar";
			}
		}else{
			$xdatos["type"] = "error";
			$xdatos["title"] = "Error";
			$xdatos["msg"] = "Debes iniciar sesion primero!";
		}
		echo json_encode($xdatos);
	}
}
