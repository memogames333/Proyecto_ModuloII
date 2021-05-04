<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
/*
$route['default_controller'] = 'ventas2/index';
$route['(:any)'] = 'ventas2/index/$1';
$route['(:any)'] = 'ventas2/ajaxPaginationData/$1';
*/
$route['default_controller'] = 'home';
/*$route['(:any)'] = 'ventas/index/$1';
$route['(:any)'] = 'ventas/ajaxPaginationData/$1';*/
$route['404_override'] = 'ErrorPage';
$route['translate_uri_dashes'] = FALSE;

$route['cambiar_contra'] = 'cambiarcontra';
$route['admin'] = 'admin/login';
$route['admin/subcategorias/(:any)'] = 'admin/subcategorias/admin/$1';
$route['producto/(:any)'] = 'producto';
$route['orden/(:any)'] = 'ordenes/orden/($1)';

$route['subcategorias/(:any)'] = 'subcategorias/index/$1';
$route['categorias/api/(:num)'] = 'rest/categorias/api/$1';
$route['productos/api/(:num)'] = 'rest/productos/api/$1';
$route['productos/api/mostrar/(:num)']["GET"] = 'rest/productos/mostrar/$1';
$route['productos/api/mostrar/(:num)/(:num)']["GET"] = 'rest/productos/mostrar/$1/$1';
$route['productos/api/search/(:any)']["GET"] = 'rest/productos/buscar/($1)';
$route['productos/api/search/(:any)/(:num)']["GET"] = 'rest/productos/buscar/($1)/$1';

$route['clientes/api']['GET'] = 'rest/clientes/api';
$route['clientes/api']['POST'] = 'rest/clientes/login';
$route['clientes/api/departamento']['GET'] = 'rest/clientes/departamentos';
$route['clientes/api/municipio/(:num)']['GET'] = 'rest/clientes/municipios/$1';
$route['clientes/api/verify']['POST'] = 'rest/clientes/verifyToken';
$route['clientes/api/register']['POST'] = 'rest/clientes/signup';
$route['clientes/api/update']['PATCH'] = 'rest/clientes/update';
$route['clientes/api/password']['PATCH'] = 'rest/clientes/updatePwd';
$route['clientes/api/wishlist']['GET'] = 'rest/clientes/wishlist_get';
$route['clientes/api/wishlist/add']['POST'] = 'rest/clientes/wishlist_add';
$route['clientes/api/wishlist/remove']['DELETE'] = 'rest/clientes/wishlist_remove';
$route['clientes/api/sucursales']['GET'] = 'rest/clientes/getsucursales';

$route['ordenes/api']['POST'] = 'rest/clientes/saveOrden';
$route['ordenes/api']['GET'] = 'rest/clientes/getOrdenes';
$route['ordenes/api/(:num)']['GET'] = 'rest/clientes/orden/$1';
$route['ordenes/api/seguimiento/(:num)']['GET'] = 'rest/clientes/seguimiento/$1';
$route['ordenes/api/tarifa']['POST'] = 'rest/clientes/getTarifa';
