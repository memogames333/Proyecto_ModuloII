<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Producto extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model("ProductoModel","producto");
		$this->load->model('UtilsModel','utils');
		$this->load->helper('utilities_helper');
    }

    public function index(){
    	$id_producto = $this->uri->segment(2);

    	if (isset($id_producto)) {

			$row = $this->producto->get_row_info($id_producto);
			$tallas = $this->producto->get_tallas($id_producto);

			isset($this->session->id_usuario)?$id_usuario=$this->session->id_usuario:$id_usuario=0;

			if($row!=false){
				$view_data = array(
					"row"=>$row,
					"tallas" => $tallas,
					"rows"=>$this->producto->get_productos_relacionados($row->id_categoria),
					"wish"=>$this->producto->get_wishlist($id_producto,$id_usuario),
				);
				$extras = array(
					"css"=>array(
						"libs/blueimp/css/blueimp-gallery.min.css",
					),
					"js"=>array(
						"libs/blueimp/js/blueimp-gallery.min.js",
						"js/funciones/producto.js",
					),
				);
				template("producto/producto",$view_data,$extras);
			}
			else{
				redirect('ErrorPage','refresh');
			}

		}
    	else{
			redirect('ErrorPage','refresh');
		}

	}

}
