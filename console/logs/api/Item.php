<?php


include APPPATH . 'libraries/REST_Controller.php';

class Item extends REST_Controller {

	  /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function __construct() {

       parent::__construct();
	//$this->load->library('rest_controller');
       $this->load->database();

    }

       

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

	public function index_get($id = 0)

	{

        if(!empty($id)){

            $data = $this->db->get_where("producto", ['id_producto' => $id])->row_array();

        }else{

            $data = $this->db->get("producto")->result();

        }

     

        $this->response($data, REST_Controller::HTTP_OK);

	}

      

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index_post()

    {

        $input = $this->input->post();

        $this->db->insert('producto',$input);

     

        $this->response(['Item created successfully.'], REST_Controller::HTTP_OK);

    } 

     

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index_put($id)

    {

        $input = $this->put();

        $this->db->update('producto', $input, array('id_producto'=>$id));

     

        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);

    }

     

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index_delete($id)

    {

        $this->db->delete('producto', array('id_producto'=>$id));

       

        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);

    }

    	

}
