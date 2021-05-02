<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("allowed_to_use")) {
	function allowed_to_use($fields = array(), &$data = array())
	{
		foreach ($data as $key => $val) {
			$data[$key] = remove_invisible_characters($data[$key]);
			$data[$key] = strip_tags($data[$key]);

			if (array_search($key, $fields) === FALSE) {
				unset($data[$key]);
			}
		}
	}
}
if (!function_exists('encrypt')) {
	function encrypt($string, $key) {
		$result = '';
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		return base64_encode($result);
	}
}

if (!function_exists('decrypt')) {
	function decrypt($string, $key) {
		$result = '';
		$string = base64_decode($string);
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
}
if (!function_exists("toArray")) {
	function toArray($data)
	{
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		if (is_array($data)) {
			return array_map('toArray', $data);
		} else {
			return $data;
		}
	}
}

if (!function_exists("parse")) {
	function parse(&$array = array(),$table=""){

		$array = toArray($array);
		foreach ($array as $key => $value){
			if($table != "" && !is_array($value))
			{
				$ci =& get_instance();
				$ci->load->model("UtilsModel","utils");
				$res = $ci->utils->query("SHOW FIELDS FROM ".$table." WHERE FIELD='".$key."'");
				if ($res != null)
				{
					$tipe =  explode("(",$res->Type)[0];
					if($key=="id" || $tipe == "int" || $tipe == "decimal" || $tipe == "tinyint" || $tipe == "float" || $tipe == "double" || $tipe == "float"){
							$array[$key] = (float)$value;
					}
				}
			}
			if(is_array($value)){
				parse($array[$key], $table);
			}
			if($key == "id" && !is_array($value))
			{
				$array[$key] = (float)$value;
			}
		}
	}
}


if (!function_exists("url")) {
	function url($field, &$array){

		$array = toArray($array);

		if(is_array($array)){
			foreach ($array as $key => $value){
				$value = toArray($value);
				foreach ($value as $key2 => $value2){
					if($key2 == $field){
						$array[$key][$field] = base_url("assets/".$value2);
					}
				}
			}
		}else{
			foreach ($array as $key => $value){
				if($key == $field){
					$array[$field] = base_url("assets/".$value);
				}
			}
		}
	}
}
