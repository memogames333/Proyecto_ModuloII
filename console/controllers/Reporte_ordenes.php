<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_ordenes extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/ReporteModel","reporte");
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
	}

	public function index()
	{
		$cat = $this->reporte->get_categorias();
		$actual = date("d-m-Y");
		$data = array(
			"desde"=> date("d-m-Y",strtotime($actual."- 1 year")),
			"hasta"=> $actual,
			"cat"=>$cat,
			"titulo"=>"Reporte de Ordenes",
			"uri"=>"admin/reporte_ordenes/generar"
		);
		$extras = array(
			'css' => array(),
			'js' => array(),
		);
		layout("admin/reportes/reporte_ventas",$data,$extras);

	}


	public function generar(){

		$desde = $this->input->post("desde");
		$hasta = $this->input->post("hasta");

		$this->load->add_package_path(APPPATH . 'third_party/fpdf');
		$this->load->library('pdf');
		$this->fpdf = new Pdf();
		$this->fpdf->SetTopMargin(0);
		$this->fpdf->SetLeftMargin(16);
		//Numeracion de paginas
		$this->fpdf->AliasNbPages();
		//Salto automatico de pagina margen de 20 mm
		$this->fpdf->SetAutoPageBreak(true, 20);
		//Agrega la pagina a trabajar
		$this->fpdf->AddPage();
		//Seteo de fuente Times New Roman 12
		$this->fpdf->SetFont('Helvetica', 'B', 14   );

		$path = base_url("assets/img/logo.png");
		//$this->fpdf->Image($path, 10, 5, 30, 25);
		$this->fpdf->Cell(280, 7,utf8_decode("IN5MIN"), 0, 1, "C");
		$this->fpdf->SetFont('Helvetica', 'B', 10);
		$this->fpdf->Cell(280, 5, utf8_decode("REPORTE DE PEDIDOS"), 0, 1, "C");

		$this->fpdf->Cell(280, 5, utf8_decode("Desde ".$desde." hasta ".$hasta), 0, 1, "C");


		$this->fpdf->Ln(5);
		$this->fpdf->SetFillColor(50, 50, 50);
		$this->fpdf->SetTextColor(255, 255, 255);
		$this->fpdf->SetFont('Helvetica', '', 9);
		$this->fpdf->Cell(10, 5, utf8_decode("N"), 1, 0, "C", 1);
		$this->fpdf->Cell(25, 5, utf8_decode("Numero de Orden"), 1, 0, "C", 1);
		$this->fpdf->Cell(40, 5, utf8_decode("Cliente"), 1, 0, "C", 1);
		$this->fpdf->Cell(50, 5, utf8_decode("Entrega"), 1, 0, "C", 1);
		$this->fpdf->Cell(35, 5, utf8_decode("Fecha Hora"), 1, 0, "C", 1);
		$this->fpdf->Cell(20, 5, utf8_decode("Estado"), 1, 0, "C", 1);
		$this->fpdf->Cell(25, 5, utf8_decode("Actualizacion"), 1, 0, "C", 1);
		$this->fpdf->Cell(25, 5, utf8_decode("Tipo de Pago"), 1, 0, "C", 1);
		$this->fpdf->Cell(15, 5, utf8_decode("Envio"), 1, 0, "C", 1);
		$this->fpdf->Cell(20, 5, utf8_decode("Total"), 1, 1, "C", 1);

		$this->fpdf->SetFillColor(255, 255, 255);
		$this->fpdf->SetTextColor(0, 0, 0);
		$y = $this->fpdf->GetY();

		$rows = $this->reporte->get_ventas_pedidos(Y_m_d($desde),Y_m_d($hasta));

		if ($rows != 0) {
			$n=1;
			$total_envio=0;
			$total=0;
			foreach ($rows as $row) {

				if($row->finalizada==1){
					$estado = "Finalizada";
				}
				else if($row->anulada==1){
					$estado = "Anulada";
				}
				else if($row->cancelada==1){
					$estado = "Cancelada";
				}
				else{
					$estado = "En Proceso";
				}

				if($row->id_estado!=0) $actualizacion = $row->estado;
				else $actualizacion = "PENDIENTE";
				$array_data = array(
					0 => array($n,5,10,"C"),
					1 => array($row->numero_orden,10,25,"L"),
					2 => array($row->usuario,25,40,"L"),
					3 => array($row->entrega,20,50,"L"),
					4 => array(d_m_Y($row->fecha)." ".hora_A_P($row->hora),40,35,"L"),
					5 => array($estado,40,20,"L"),
					6 => array($actualizacion,12,25,"L"),
					7 => array($row->tipo,20,25,"C"),
					8 => array("$".dinero($row->envio),5,15,"R"),
					9 => array("$".dinero($row->total),20,20,"R"),

				);

				$data=array_procesor($array_data);
				$total_lineas=count($data[0]["valor"]);
				$total_columnas=count($data);

				for ($i=0; $i < $total_lineas; $i++) {
					for ($j=0; $j < $total_columnas; $j++) {
						$salto=0;
						$abajo="LRT";
						if ($j==$total_columnas-1) {
							$salto=1;
						}
						if ($i<=$total_lineas-1) {
							$abajo="LR";
						}
						if ($i==$total_lineas-1) {
							$abajo="LRB";
						}
						$this->fpdf->Cell($data[$j]["size"][$i],5,utf8_decode($data[$j]["valor"][$i]),$abajo,$salto,$data[$j]["aling"][$i]);
					}
				}
				$n++;
				$total_envio +=$row->envio;
				$total +=$row->total;
			}
			$this->fpdf->Ln(5);
			$this->fpdf->Cell(205, 5,"", 0, 0, "R", 1);
			$this->fpdf->Cell(25, 5, utf8_decode("Total de envio"), 1, 0, "R", 1);
			$this->fpdf->Cell(35, 5, utf8_decode("$".dinero($total_envio)), 1, 1, "R", 1);

			$this->fpdf->Cell(205, 5, "", 0, 0, "R", 1);
			$this->fpdf->Cell(25, 5, utf8_decode("Subtotal"), 1, 0, "R", 1);
			$this->fpdf->Cell(35, 5, utf8_decode("$".dinero($total)), 1, 1, "R", 1);

			$this->fpdf->Cell(205, 5, "", 0, 0, "R", 1);
			$this->fpdf->Cell(25, 5, utf8_decode("Total"), 1, 0, "R", 1);
			$this->fpdf->Cell(35, 5, utf8_decode("$".dinero($total+$total_envio)), 1, 1, "R", 1);
		}

		ob_clean();
		$this->fpdf->Output("reporte_ventas.pdf", "I");
	}

}

/* End of file Dashboard.php */
