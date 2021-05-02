<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catering extends CI_Controller {

	public function index()
	{
		$data = array(
			'clave' => 4,
		);
		layout('catering',$data,'');
	}
}
