<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

	public function index()
	{
		$data = array(
			'clave' => 3,
		);
		layout('menu',$data,'');
	}
}
