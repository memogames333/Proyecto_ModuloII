<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists("encrypt")){
	function encrypt($val){
		$ci =& get_instance();
		return $ci->encryption->encrypt($val);
	}
}

if(!function_exists("decrypt")){
	function decrypt($val){
		$ci =& get_instance();
		return $ci->encryption->decrypt($val);
	}
}


if(!function_exists("get_code")){
	function get_code($length = 6)
	{
		$number = '0123456789';
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$numbersLength = strlen($number);
		$randomString = '';
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		for ($i = 0; $i < $length; $i++)
		{
				$randomString .= $number[rand(0, $numbersLength - 1)];
		}
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}
}


if(!function_exists("parse_values")){
	function parse_values(&$data = array()){

		$data = stdToArray($data);

		foreach ($data as $key => $val){
			foreach ($val as $field => $val_field){
				if(is_numeric($val_field)){
					$data[$key][$field] = (float)$val_field;
				}
			}
		}
	}
}

if(!function_exists("stdToArray")){
	function stdToArray($obj){
		$reaged = (array)$obj;
		foreach($reaged as $key => &$field){
			if(is_object($field))$field = stdToArray($field);
		}
		return $reaged;
	}
}

if(!function_exists("img_exist_header")){
    function img_exist_header($url)
    {

        if (!$url) return  base_url("assets/images/not-found.svg");

				//$url = str_replace("img/","",$url);
				$url="console/assets/".$url;
        $noimage= base_url("assets/images/not-found.svg");
				if (file_exists($url)) {
					return base_url($url);
				}
				else {
					return $noimage;
				}

    }
}

if(!function_exists("image_array")){
    function image_array($array_img  = array())
    {

        $noimage= base_url("assets/images/not-found.svg");

				$arry_return=  array();

				$i=0;
				foreach ($array_img as $key => $value) {

					if (trim($value)!="") {
						// code...
						$url="console/assets/".$value;
						if (file_exists($url)) {
							$arry_return[$i]=base_url($url);
						}
						else {
							$arry_return[$i]=$noimage;
						}
						$i++;
					}
				}

				if ($i==0) {
					$arry_return[$i]=$noimage;
				}

				return $arry_return;
    }
}
