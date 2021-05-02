<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends CI_Controller {

	public function index()
	{
		$data = array(
			'clave' => 5,
		);
		layout('location',$data,'');
	}
}
