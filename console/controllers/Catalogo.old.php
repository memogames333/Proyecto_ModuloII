<?php
class Catalogo extends CI_Controller{
	function __construct() {
        parent::__construct();

         // Load pagination library
         $this->load->library('pagination');
        $this->load->library('ajax_pagination');
        //load post model
        $this->load->model('Catalogo_model');
          $this->load->model('Categorias_model');
          $this->load->helper('utilities_helper');
        //per page limit
        $this->perPage=30;
    }

	public function index(){
        $data = array();

        // Get record count
        $conditions['returnType'] = 'count';
        $totalRec = $this->Catalogo_model->getRows($conditions);

        // Pagination configuration
        $config['target']      = '#dataList';
        $config['base_url']    = base_url('catalogo/ajaxPaginationData');
        $config['total_rows']  = $totalRec;
        $config['per_page']    = $this->perPage;
        $config['link_func']   = 'searchFilter';

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
        $conditions = array(
            'limit' => $this->perPage
        );
        $data['productos'] = $this->Catalogo_model->getRows($conditions);
        $data['numrows'] = $this->Catalogo_model->numRows();
        $data['categorias'] =$this->Categorias_model->getRows();
        // Load the list page view

		template("catalogo/catalog",$data,"");
       // $this->load->view('catalogo/catalog', $data);

    }
	function getIdCat(){
        // Define offset
        $cat = $this->input->post('cat');
        if($cat!="")
			$data['idCat']=$this->Categorias_model->getIdCat($cat);
		else
			$data['idCat']=0;
		 //$data['idCat']=random_int(1, 15);
		echo json_encode($data);
     }
    function ajaxPaginationData(){
        // Define offset
        $page = $this->input->post('page');
        if(!$page){
            $offset = 0;
        }else{
            $offset = $page;
        }

        // Set conditions for search and filter
        $keywords = $this->input->post('keywords');
        $sortBy = $this->input->post('sortBy');
        $byCat = $this->input->post('byCat');
        if(!empty($keywords)){
            $conditions['search']['keywords'] = $keywords;
        }
        if(!empty($sortBy)){
            $conditions['search']['sortBy'] = $sortBy;
        }
          if(!empty($byCat)){
            $conditions['search']['byCat'] = $byCat ;
        }
        // Get record count
        $conditions['returnType'] = 'count';
        $totalRec = $this->Catalogo_model->getRows($conditions);

        // Pagination configuration
        $config['target']      = '#dataList';
        $config['base_url']    = base_url('catalogo/ajaxPaginationData');
        $config['total_rows']  = $totalRec;
        $config['per_page']    = $this->perPage;
        $config['link_func']   = 'searchFilter';

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
       // $data['catalogo'] = $this->Catalogo_model->getRows($conditions);
          $data['productos'] = $this->Catalogo_model->getRows($conditions);
		  $data['numrows'] = $this->Catalogo_model->numRows();

        // Load the data list view
        $this->load->view('catalogo/catalogo-pagination-data', $data, false);

		//template("catalogo/catalogo-pagination-data', ",$data,"");
    }
    //agregar al carro
    function add_to_cart(){

		$data = array(
			'id' => $this->input->post('product_id'),
			'name' => $this->input->post('product_name'),
			'price' => $this->input->post('product_price'),
			'qty' => $this->input->post('quantity'),
			'image' => $this->input->post('image'),
		);
		$this->cart->insert($data);
	}

	function get_image($id){
		 $data['img_product'] = $this->Catalogo_model->getImage($id);
	}
	function show_cart_page(){
		$data['listaprod'] = $this->cart->contents();
		$this->load->view('catalogo/cart', $data);

	}
	function cart(){
		$data['listaprod'] = $this->cart->contents();

		template("catalogo/cart",$data,"");
		//$this->load->view('catalogo/cart', $data);

	}
	function show_cart(){
		$data['listaprod'] = $this->cart->contents();


		$this->load->view('catalogo/cart_table_reload', $data);

}

	function load_cart_page(){
		echo $this->cart();

	}
	function load_cart(){
		echo $this->show_cart();
	}
	function delete_cart(){
		$data = array(
		'rowid' => $this->input->post('row_id'),
		'qty' => 0,
		);
		$this->cart->update($data);
		echo $this->show_cart();
	}
	function delete_all_cart(){
		$this->cart->destroy();
		echo $this->show_cart();
	//	echo $this->show_cart_total();
	}

	function update_cart(){
		// Recieve post values,calcute them and update
		$price = $this->input->post('price');
		$qty= $this->input->post('qty');
		if($qty<1){
			$qty=1;
		}
		else{
		$amount = $price * $qty;
		$data = array(
		'rowid' => $this->input->post('row_id'),
		'qty' => $qty,
		'price' => $price,
		'amount' => $amount,
		);
		$this->cart->update($data);
		}
		//echo $this->load_cart();
		echo $this->show_cart();

	}
	function update_cart_list(){
		$no=0;
			$output="";
			$output .='<li class="total bg-theme">
						<span class="foat-left"><strong>Total</strong>: $'.number_format($this->cart->total(), 2, '.', '').'</span>
						<a href="'.base_url("catalogo/cart").'" class="butn-style2 small white float-right w-auto"><span>Ver Carrito</span></a>
					</li>';
			foreach ($this->cart->contents() as $items) {
							$no++;
							$output .='
							 <li>
							 <a href="shop-cart.html#!" class="photo"><img src="'.$items['image'].'" class="cart-thumb" alt="..." /></a>
							<h6><a href="shop-cart.html#!">'.$items['name'].'</a></h6>
							<p>'.$items['qty'].' x - <span class="price">$'.number_format($items['price'], 2, '.', '').'</span></p>
                           </li>';
					}
			$output .='<li class="total bg-theme">
						<span class="foat-left"><strong>Total</strong>: $'.number_format($this->cart->total(), 2, '.', '').'</span>
						<a href="'.base_url("catalogo/cart").'" class="butn-style2 small white float-right w-auto"><span>Ver Carrito</span></a>
					</li>';
          echo $output;
	}
	function total_items_list(){
		$total=0;
		$output=''.$this->cart->total_items();
         echo $output;
	}

}

?>
