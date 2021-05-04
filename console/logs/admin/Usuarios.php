<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	/*
	Global table name
	*/
	private $table = "usuarios_admin";

	function __construct()
	{
		parent::__construct();
		$this->load->model('UtilsModel',"utils");
		$this->load->Model("admin/UsuariosModel","usuarios");
		$this->load->helper('template_admin_helper');
		$this->load->helper('utilities_helper');
	}

	public function index()
	{
		$data = array(
			"titulo"=> "Usuarios",
			"icono"=> "fa fa-users",
			"buttons" => array(
				0 => array(
					"icon"=> "fa fa-plus",
					'url' => 'usuarios/agregar',
					'txt' => 'Agregar Usuario',
					'modal' => false,
				),
			),
			"table"=>array(
				"ID"=>1,
				"Usuario"=>2,
				"Nombre"=>2,
				"Tipo de Usuario"=>2,
				"Estado"=>1,
				"Acciones"=>1,
			),
			"urljs"=>"funciones_usuarios.js",
		);
		$extras = array(
			'css' => array(),
			'js' => array(),
		);
		layout("admin/template/admin",$data,$extras);
	}

	function get_data(){

		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));

		$order = $this->input->post("order");
		$search = $this->input->post("search");
		$search = $search['value'];
		$col = 0;
		$dir = "";
		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$dir = $o['dir'];
			}
		}

		if ($dir != "asc" && $dir != "desc") {
			$dir = "desc";
		}
		$valid_columns = array(
			0 => 'id_usuario',
			1 => 'nombre',
			2 => 'usuario',
		);
		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}

		$row = $this->usuarios->get_collection($order, $search, $valid_columns, $length, $start, $dir);

		if ($row != 0) {
			$data = array();
			foreach ($row as $rows) {

				$menudrop = "<div class='btn-group'>
					<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
					<ul class='dropdown-menu dropdown-primary'>";
				$filename = base_url("admin/usuarios/editar/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_usuario. "' ><i class='fa fa-pencil' ></i> Editar</a></li>";

				$filename = base_url("admin/usuarios/permisos/");
				$menudrop .= "<li><a role='button' href='" . $filename.$rows->id_usuario. "' ><i class='fa fa-list-ul' ></i> Permisos</a></li>";

				$state = $rows->activo;
				if($state==1){
					$txt = "Desactivar";
					$show_text = "<span class='badge badge-primary font-bold'>Activo<span>";
					$icon = "fa fa-toggle-off";
				}
				else{
					$txt = "Activar";
					$show_text = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$icon = "fa fa-toggle-on";
				}

				if($rows->admin==1)	$type_user = "<span class='badge badge-warning font-bold'>Administrador<span>";
				else $type_user = "<span class='badge badge-info font-bold'>Normal<span>";

				$menudrop .= "<li><a class='state_change' data-state='$txt'  id=" . $rows->id_usuario . " ><i class='$icon'></i> $txt</a></li>";

				$menudrop .= "<li><a class='delete_row'  id=" . $rows->id_usuario . " ><i class='fa fa-trash'></i> Eliminar</a></li>";
				$menudrop .= "</ul></div>";


				$data[] = array(
					$rows->id_usuario,
					$rows->nombre,
					$rows->usuario,
					$type_user,
					$show_text,
					$menudrop,
				);
			}
			$total = $this->usuarios->total_rows();
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $total,
				"recordsFiltered" => $total,
				"data" => $data
			);
		} else {
			$data[] = array(
				"",
				"",
				"No se encontraron registros",
				"",
				"",
				"",
			);
			$output = array(
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => $data
			);
		}
		echo json_encode($output);
		exit();
	}

	function agregar(){
		if($this->input->method(TRUE) == "GET"){
			$data = array(
				"urljs"=>"funciones_usuarios.js",
			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
				),
			);
			layout("admin/usuarios/agregar_usuario",$data,$extras);
		}
		else if($this->input->method(TRUE) == "POST"){
			$nombre = $this->input->post("nombre");
			$usuario = $this->input->post("usuario");
			$password = $this->input->post("password");
			$pass = encrypt($password,"eNcRiPt_K3Y");
			$admin = $this->input->post("tipo_usuario");
			$existe = $this->usuarios->exits_row($usuario);
			if($existe==0){
				$data = array(
					"nombre"=>$nombre,
					"usuario"=>$usuario,
					"admin"=>$admin,
					"clave"=>$pass,
					"activo"=>1,
				);
				$this->utils->begin();
				$insert = $this->utils->insert($this->table,$data);
				if($insert){
					$this->utils->commit();
					$xdatos["type"]="success";
					$xdatos['title']='Información';
					$xdatos["msg"]="Registo ingresado correctamente!";
				}
				else {
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al ingresar el registro";
				}
			}
			else{
				$xdatos["type"]="error";
				$xdatos['title']='Erro';
				$xdatos["msg"]="Ya existe un registro con el mismo nombre de usuario!";
			}
			echo json_encode($xdatos);
		}
	}

	function editar($id=-1){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(4);
			$row = $this->usuarios->get_row_info($id);
			if($row && $id!=""){
				$password = decrypt($row->clave,"eNcRiPt_K3Y");
				$data = array(
					"row"=>$row,
					"password"=>$password,
					"urljs"=>"funciones_usuarios.js",
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
					),
				);
				layout("admin/usuarios/editar_usuario",$data,$extras);
			}else{
				redirect('admin/errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$nombre = $this->input->post("nombre");
			$usuario = $this->input->post("usuario");
			$password = $this->input->post("password");
			$pass = encrypt($password,"eNcRiPt_K3Y");
			$admin = $this->input->post("tipo_usuario");
			$id_usuario = $this->input->post("id_usuario");
			$where = " id_usuario='".$id_usuario."'";
			$existe = $this->usuarios->exits_row_edit($usuario,$id_usuario);
			if($existe==0){
				$data = array(
					"nombre"=>$nombre,
					"usuario"=>$usuario,
					"admin"=>$admin,
					"clave"=>$pass,
				);
				$this->utils->begin();
				$insert = $this->utils->update($this->table,$data,$where);
				if($insert){
					$this->utils->commit();
					$xdatos["type"]="success";
					$xdatos['title']='Información';
					$xdatos["msg"]="Registo editado correctamente!";
				}
				else {
					$this->utils->rollback();
					$xdatos["type"]="error";
					$xdatos['title']='Alerta';
					$xdatos["msg"]="Error al editar el registro";
				}
			}
			else{
				$xdatos["type"]="error";
				$xdatos['title']='Erro';
				$xdatos["msg"]="Ya existe un registro con el mismo nombre de usuario!";
			}
		echo json_encode($xdatos);
		}
	}

	function delete(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$where = " id_usuario ='".$id."'";
			$this->utils->begin();
			$delete = $this->utils->delete($this->table,$where);
			if($delete) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro eliminado con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser eliminado!";
			}
			echo json_encode($data);
		}
	}

	function state_change(){
		if($this->input->method(TRUE) == "POST"){
			$id = $this->input->post("id");
			$active = $this->usuarios->get_state($id);
			if($active==0){
				$state = 1;
				$text = 'activado';
			}else{
				$state = 0;
				$text = 'desactivado';
			}
			$form = array(
				"activo" =>$state
			);
			$where = " id_usuario ='".$id."'";
			$this->utils->begin();
			$update = $this->utils->update($this->table,$form,$where);
			if($update) {
				$this->utils->commit();
				$data["type"] = "success";
				$data["title"] = "Información";
				$data["msg"] = "Registro $text con exito!";
			}
			else {
				$this->utils->rollback();
				$data["type"] = "Error";
				$data["title"] = "Alerta!";
				$data["msg"] = "Registro no pudo ser $text!";
			}
			echo json_encode($data);
			exit();
		}
	}

	function permisos(){
		if($this->input->method(TRUE) == "GET"){
			$id = $this->uri->segment(4);
			$row = $this->usuarios->get_row_info($id);
			if($row && $id!=""){

				$permissions = $this->usuarios->get_permissions($id);
				$menus = $this->usuarios->get_menu();
				$controller_base = $this->usuarios->get_controller();
				$controller = array();
				foreach ($menus as $menu)
				{
					$id_menu = $menu->id_menu;
					$controller[$id_menu] = array_filter($controller_base, function($controller) use ($id_menu)
					{
						return $controller->id_menu == $id_menu;
					});
				}
				$data = array(
					'row'=>$row,
					'controller'=>$controller,
					'menu'=>$menus,
					'permissions_user'=>$permissions
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						0 => "admin/js/funciones/funciones_usuarios.js"
					),
				);

				layout("admin/usuarios/permisos",$data,$extras);
			}else{
				redirect('admin/errorpage');
			}
		}
		else if($this->input->method(TRUE) == "POST"){
			$id_usuario = $this->input->post("id_usuario");
			$module = $this->input->post("modules");
			$modules = explode(",",$module);
			$admin = intval($this->input->post("admin"));

			$this->utils->begin();
			if($admin==1){
				$table  = "usuarios_admin";
				$form = array("admin"=>1);
				$where = " id_usuario ='".$id_usuario."'";
				$update = $this->utils->update($table,$form,$where);
				if($update){
					$this->utils->commit();
					$data['type'] = 'success';
					$data['title'] = 'Éxito';
					$data['msg'] = 'Permisos asignados exitosamente!';
				}else {
					$this->utils->rollback();
					$data['type'] = 'error';
					$data['title'] = 'Error';
					$data['msg'] = 'No se pudo guardar los permisos!';
				}
			}else{
				$table  = "usuarios_admin";
				$form = array("admin"=>0);
				$where = " id_usuario ='".$id_usuario."'";
				$update = $this->utils->update($table,$form,$where);
				if($update){
					$tablep = "permisos_usuario";
					$wherep = " id_usuario='".$id_usuario."'";
					$delete = $this->utils->delete($tablep,$wherep);
					if($delete){
						for ($i=0;$i<count($modules);$i++){
							$form_data = array(
								"id_usuario"=>$id_usuario,
								"id_modulo"=>$modules[$i],
							);
							$insert = $this->utils->insert($tablep,$form_data);
						}
						if($insert){
							$this->utils->commit();
							$data['type'] = 'success';
							$data['title'] = 'Éxito';
							$data['msg'] = 'Permisos asignados exitosamente!';
						}
					}else{
						$this->utils->rollback();
						$data['type'] = 'error';
						$data['title'] = 'Error';
						$data['msg'] = 'No se pudo guardar los permisos!';
					}
				}else {
					$this->utils->rollback();
					$data['type'] = 'error';
					$data['title'] = 'Error';
					$data['msg'] = 'No se pudo guardar los permisos!';
				}
			}
			echo json_encode($data);
		}
	}

}

/* End of file Usuarios.php */
