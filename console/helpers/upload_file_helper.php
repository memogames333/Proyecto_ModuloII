<?php
if(!function_exists("upload_image")){

	function upload_image($file, $path)
	{
		$ci =& get_instance();
		$_FILES['file']['name'] = $_FILES[$file]['name'];
		$_FILES['file']['type'] = $_FILES[$file]['type'];
		$_FILES['file']['tmp_name'] = $_FILES[$file]['tmp_name'];
		$_FILES['file']['error'] = $_FILES[$file]['error'];
		$_FILES['file']['size'] = $_FILES[$file]['size'];

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'jpg|jpeg|png|bmp|csv';

		$info = new SplFileInfo( $_FILES[$file]['name']);
		$name = uniqid(date("dmYHi")).".".$info->getExtension();
		$config['file_name'] = $name;
		$ci->upload->initialize($config);
		$ci->load->library('upload', $config);
		if ($ci->upload->do_upload('file')) return $name;
		else return null;
	}

}

if(!function_exists("resize_image"))
{
	function resize_image($file,$path,$width,$height,$quality,$thumb=0,$path_thumb="")
	{
		/*
		 * PHP GD2 is required to use this library
		 * */
		$ci =& get_instance();
		$ci->load->library('image_lib');

		if($thumb==1){
			$img_array = array(
				'image_library' => 'gd2',
				'source_image' => $path.$file,
				'new_image' => $path_thumb,
				'create_thumb' => FALSE,//tell the CI do not create thumbnail on image
				'maintain_ratio' => TRUE,
				'width' => $width,//new size of image
				'height' => $height,//new size of image
				'quality'=>$quality,
			);
		}
		else{
			$img_array = array(
				'image_library' => 'gd2',
				'source_image' => $path.$file,
				'create_thumb' => FALSE,//tell the CI do not create thumbnail on image
				'maintain_ratio' => TRUE,
				'width' => $width,//new size of image
				'height' => $height,//new size of image
				'quality'=>$quality,
			);
		}
		$ci->image_lib->clear();
		$ci->image_lib->initialize($img_array);
		$ci->image_lib->resize();
	}
}
