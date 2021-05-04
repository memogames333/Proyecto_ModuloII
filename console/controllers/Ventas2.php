<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
 
class Ventas2 extends CI_Controller { 
     
    function __construct() { 
        parent::__construct(); 
         
        // Load pagination library 
        $this->load->library('ajax_pagination'); 
         
        // Load post model 
        $this->load->model('Ventas2_model'); 
         
        // Per page limit 
        $this->perPage = 25; 
    } 
     
    public function index(){ 
        $data = array(); 
         
        // Get record count 
        $conditions['returnType'] = 'count'; 
        $totalRec = $this->Ventas2_model->getRows($conditions); 
         
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    = base_url('ventas2/ajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
        $config['link_func']   = 'searchFilter'; 
         
        // Initialize pagination library 
        $this->ajax_pagination->initialize($config); 
         
        // Get records 
        $conditions = array( 
            'limit' => $this->perPage 
        ); 
        $data['ventas'] = $this->Ventas2_model->getRows($conditions); 
         
        // Load the list page view 
        $this->load->view('ventas/index', $data); 
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
        $totalRec = $this->Ventas2_model->getRows($conditions); 
         
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    = base_url('ventas2/ajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
        $config['link_func']   = 'searchFilter'; 
         
        // Initialize pagination library 
        $this->ajax_pagination->initialize($config); 
         
        // Get records 
        $conditions['start'] = $offset; 
        $conditions['limit'] = $this->perPage; 
        unset($conditions['returnType']); 
        $data['ventas'] = $this->Ventas2_model->getRows($conditions); 
         
        // Load the data list view 
        $this->load->view('ventas/ajax-pagination-data', $data, false); 
    } 
}
