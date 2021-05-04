<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ErrorPage extends CI_Controller {

	public function index()
	{
		$this->load->view("404","");
	}

}

/* End of file Controllername.php */
