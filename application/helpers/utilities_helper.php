<?php
if(!function_exists("Y_m_d"))
{
	function Y_m_d($fecha)
	{
		$a = substr($fecha,6,4);
		$mes = substr($fecha,3,2);
		$dia = substr($fecha,0,2);
		$fecha = "$a-$mes-$dia";
		return $fecha;
	}
}
if(!function_exists("d_m_Y"))
{
	function d_m_Y($fecha)
	{
		$a = substr($fecha,0,4);
		$mes = substr($fecha,5,2);
		$dia = substr($fecha,8,2);
		$fecha = "$dia-$mes-$a";
		return $fecha;
	}
}
if(!function_exists("hora_A_P"))
{
	function hora_A_P($hora)
	{
		$hora_pre = date_create($hora);
		$hora_pos = date_format($hora_pre, 'g:i A');
		return $hora_pos;
	}
}
if(!function_exists("quitar_tildes"))
{
	function quitar_tildes($cadena)
	{
		$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹"," ");
		$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","_");
		$texto = str_replace($no_permitidas, $permitidas ,$cadena);
		return $texto;
	}
}
if(!function_exists("diferenciaDias"))
{
	function diferenciaDias($inicio, $fin)
	{
		$inicio = strtotime($inicio);
		$fin = strtotime($fin);
		$dif = $fin - $inicio;
		$diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
		return ceil($diasFalt);
	}
}
if(!function_exists("divtextlin"))
{
	function divtextlin( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
		$wrappedarr = array();
		$wrappedtext = wordwrap( $text, $width, $break , true );
		$wrappedtext = trim( $wrappedtext );
		$arr = explode( $break, $wrappedtext );
		return $arr;
	}
}
if(!function_exists("array_procesor"))
{
	function array_procesor($array)
	{
		$ygg=0;
		$maxlines=1;
		$array_a_retornar=array();
		foreach ($array as $key => $value) {
			/*Descripcion*/
			$nombr=$value[0];
			/*character*/
			$longitud=$value[1];
			/*fpdf width*/
			$size=$value[2];
			/*fpdf alignt*/
			$aling=$value[3];
			if(strlen($nombr) > $longitud)
			{
				$i=0;
				$nom = divtextlin($nombr, $longitud);
				foreach ($nom as $nnon)
				{
					$array_a_retornar[$ygg]["valor"][]=$nnon;
					$array_a_retornar[$ygg]["size"][]=$size;
					$array_a_retornar[$ygg]["aling"][]=$aling;
					$i++;
				}
				$ygg++;
				if ($i>$maxlines) {
					// code...
					$maxlines=$i;
				}
			}
			else {
				// code...
				$array_a_retornar[$ygg]['valor'][]=$nombr;
				$array_a_retornar[$ygg]['size'][]=$size;
				$array_a_retornar[$ygg]["aling"][]=$aling;
				$ygg++;

			}
		}

		$ygg=0;
		foreach($array_a_retornar as $keys)
		{
			for ($i=count($keys["valor"]); $i <$maxlines ; $i++) {
				// code...
				$array_a_retornar[$ygg]["valor"][]="";
				$array_a_retornar[$ygg]["size"][]=$array_a_retornar[$ygg]["size"][0];
				$array_a_retornar[$ygg]["aling"][]=$array_a_retornar[$ygg]["aling"][0];
			}
			$ygg++;
		}
		return $array_a_retornar;

	}
}
if(!function_exists("dinero")){
	function dinero($dinero)
	{
		return number_format($dinero,"2",".",",");
	}
}
if(!function_exists("restar_meses")){
	function restar_meses($fecha, $cantidad)
	{
		$nuevafecha = strtotime ( '-'.$cantidad.' month' , strtotime ( $fecha ) ) ;
		$nuevafecha = date ( 'Y-m-d' , $nuevafecha );
		return $nuevafecha;
	}
}
if(!function_exists("sumar_meses")){
	function sumar_meses($fecha, $cantidad)
	{
		$nuevafecha = strtotime ( '+'.$cantidad.' month' , strtotime ( $fecha ) ) ;
		$nuevafecha = date ( 'Y-m-d' , $nuevafecha );
		return $nuevafecha;
	}
}
if(!function_exists("nombre_mes")){
	function nombre_mes($n){
		$mes = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		return $mes[$n-1];
	}
}
if(!function_exists("edad_decimal")){
	function edad_decimal($fecha){
		$dob_day = substr($fecha,8,2);
		$dob_month = substr($fecha,5,2);
		$dob_year = substr($fecha,0,4);
		$year   = gmdate('Y');
		$month  = gmdate('m');
		$day    = gmdate('d');
		//seconds in a day = 86400
		$days = (mktime(0,0,0,$month,$day,$year) - mktime(0,0,0,$dob_month,$dob_day,$dob_year))/86400;
		return $days / 365.242199;
	}
}

if(!function_exists("salto")){
	function salto($lines,$n){
		$ln=$lines-$n;
		for($i=0;$i<$ln;$i++){
			echo "&nbsp;"."<br>";
		}
	}
}

if(!function_exists("img_exist")){
	function img_exist($url = NULL)
	{
		if (!$url) return FALSE;
		$rutaProd= base_url()."assets/";
		$noimage = 'img/productos/no_disponible.png';
		$noimage=$rutaProd."img/productos/no_disponible.png";
		$headers = get_headers($url);
		return stripos($headers[0], "200 OK") ? $url : $noimage;
	}
}
