<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	
	public function __construct()
	{
		parent::__construct();
		validar_session($this);
		$this->load->helper('template_admin_helper');
		$this->load->model("admin/Dashboard_model","dashboard");
		$this->load->helper("utilities_helper");
	}
	
	/* 
	* Carga la vista de dashboard
	* Envia datos a mostrar en los widgets 
	* Envia datos a mostrar en las tablas
	*/
	public function index()
	{

			$data = array(
//				"saldo"=>$saldo,
//				"tipo"=>$tipo,
				"urljs"=>"funciones_dashboard.js",
//				"permisos"=>$permisos,
//				"vacaciones"=>$vacaciones,
//				"solP"=>$solP->num,
//				"solV"=>$solV->num
			);


		//Carga la vista
		layout('admin/dashboard',$data);
	}
	
	/*
	* Generar json de la grafica de permisos para el administrador
	* Recibe un rango de fechas
	* Devuelve json con los datos
	*/
	function grafica_productos_vendidos(){
		//$inicio = restar_meses(date("Y-m-d"),6);
		/*for($i=0; $i<6; $i++)
		{
			$a = explode("-",$inicio)[0];
			$m = explode("-",$inicio)[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$start = "$a-$m-01";
			$end = "$a-$m-$ult";*/
			$row = $this->dashboard->grafica_productos_vendidos();
			//$total = $row->total;
			/*$data[] = array(
				"total" => $row->total,
				"producto" => $row->producto,
			);*/
			//$inicio = sumar_meses($start,1);
		//}
		echo json_encode($row);
	}

	/*
	* Generar json de la grafica de vacaciones para el administrador
	* Recibe un rango de fechas
	* Devuelve json con los datos
	*/
	function grafica_venta_mes(){
		$inicio = restar_meses(date("Y-m-d"),6);
		for($i=0; $i<6; $i++)
		{
			$a = explode("-",$inicio)[0];
			$m = explode("-",$inicio)[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$start = "$a-$m-01";
			$end = "$a-$m-$ult";
			$row = $this->dashboard->grafica_vacacion($start,$end);
			$total = $row->total;
			$data[] = array(
				"total" => $total,
				"mes" => nombre_mes($m),
			);
			$inicio = sumar_meses($start,1);
		}
		echo json_encode($data);
	}

	function grafica_ingreso_mes(){

		$inicio = restar_meses(date("Y-m-d"),5);
		for($i=0; $i<6; $i++)
		{
			$fecha = explode("-",$inicio);
			$a = $fecha[0];
			$m = $fecha[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$start = "$a-$m-01";
			$end = "$a-$m-$ult";
			$row = $this->dashboard->grafica_ingreso_mes($start,$end);
			$total = $row->total;
			$data[] = array(
				"total" => dinero($total),
				"mes" => nombre_mes($m),
			);
			$inicio = sumar_meses($start,1);
		}
		echo json_encode($data);
	}

	function grafica_ordenes(){

		$inicio = restar_meses(date("Y-m-d"),5);
		for($i=0; $i<6; $i++)
		{
			$fecha = explode("-",$inicio);
			$a = $fecha[0];
			$m = $fecha[1];
			$ult = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			$start = "$a-$m-01";
			$end = "$a-$m-$ult";
			$row1 = $this->dashboard->grafica_ordenes_finalizada($start,$end);
			$row2 = $this->dashboard->grafica_ordenes_anulada($start,$end);
			$row3 = $this->dashboard->grafica_ordenes_cancelada($start,$end);
			$data[] = array(
				"finalizada" => $row1->total,
				"anulada" => $row2->total,
				"cancelada" => $row3->total,
				"mes" => nombre_mes($m),
			);
			$inicio = sumar_meses($start,1);
		}
		echo json_encode($data);
	}

}
