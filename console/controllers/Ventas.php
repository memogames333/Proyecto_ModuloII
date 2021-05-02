<?php
class Ventas extends CI_Controller{
	function __construct() {
        parent::__construct();
        //load pagination library
        $this->load->library('pagination');
         // Load pagination library 
        $this->load->library('ajax_pagination'); 
        //load post model
        $this->load->model('Ventas_model');
        //per page limit
        $this->perPage=30;
    }
    
	public function view($page = 'venta2'){
        if ( ! file_exists(APPPATH.'views/ventas/'.$page.'.php'))
        {            
			show_404();
        }
		$length=50; 
        $start=1;      
        $ventass = $this->Ventas_model->get_products($length, $start);
        $this->load->view("ventas/venta1", array("productos" => $ventass));        
	}
	
	public function index(){ 
        $data = array(); 
         
        // Get record count 
        $conditions['returnType'] = 'count'; 
        $totalRec = $this->Ventas_model->getRows($conditions); 
         
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    = base_url('ventas/ajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
        $config['link_func']   = 'searchFilter'; 
         
           //styling
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
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
        $data['productos'] = $this->Ventas_model->getRows($conditions); 
        $data['numrows'] = $this->Ventas_model->numRows();
        // Load the list page view 
        $this->load->view('ventas/venta1', $data); 
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
        if(!empty($keywords)){ 
            $conditions['search']['keywords'] = $keywords; 
        } 
        if(!empty($sortBy)){ 
            $conditions['search']['sortBy'] = $sortBy; 
        } 
         
        // Get record count 
        $conditions['returnType'] = 'count'; 
        $totalRec = $this->Ventas_model->getRows($conditions); 
         
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    = base_url('ventas/ajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
        $config['link_func']   = 'searchFilter'; 
         
           //styling
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
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
       // $data['ventas'] = $this->Ventas_model->getRows($conditions); 
          $data['productos'] = $this->Ventas_model->getRows($conditions); 
        $data['numrows'] = $this->Ventas_model->numRows();
        // Load the data list view 
        $this->load->view('ventas/ajax-pagination-data2', $data, false); 
    } 
}

?>
