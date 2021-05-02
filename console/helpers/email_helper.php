<?php
if (!function_exists('send_email')) {

	function send_email($data = array()) {
		$ci =& get_instance();
		return  $ci->load->view('template/confirm_email',$data,true);
	}
	function send_email_password($data = array()) {
		$ci =& get_instance();
		return  $ci->load->view('template/reset_password_email',$data,true);
	}
	function send_email_contact($data = array()) {
		$ci =& get_instance();
		return  $ci->load->view('template/contact_email',$data,true);
	}
	/*
	function send_email_shop($data = array()) {
		$ci =& get_instance();
		return  $ci->load->view('checkout/checout_bill',$data,true);
	}
	* */
}
