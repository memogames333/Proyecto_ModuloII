<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends CI_Controller {

	public function index()
	{
		$data = array(
			'clave' => 2,
		);
		layout('gallery',$data,'');
	}
}
