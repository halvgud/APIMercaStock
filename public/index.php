<?php session_start();
if (PHP_SAPI == 'cli-server') {
	// To help the built-in PHP dev server, check if the request was actually for
	// something which should probably be served as a static file
	$file = __DIR__ . $_SERVER['REQUEST_URI'];
	if (is_file($file)) {
		return false;
	}
}
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
require __DIR__ . '/../vendor/autoload.php';
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/middleware.php';
//$app->add(new \Slim\Middleware\ContentTypes());
require __DIR__ . '/../src/routes.php';
require_once("usuario.php");
require_once("sucursal.php");
require_once("MercaStock.php");
require_once("departamento.php");
require_once("categoria.php");
require_once("articulo.php");
require_once("bitacora.php");
require_once("inventario.php");
require_once "Roles.php";
require_once "PrivilegiosUsuario.php";
require_once "permisos.php";
require_once("parametros.php");
require_once("nivel_autorizacion.php");
require_once("sexo.php");
require_once("importar.php");
require_once('exportar.php');
require_once("concepto.php");
require_once("tipo_pago.php");
require_once("detalles_venta.php");
require_once("general.php");
//require_once("tipo_pago.php");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Usuario Catch
$app->post('/usuario/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
	return usuario::seleccionar($request,$response);
});
$app->post('/usuario/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
	return usuario::insertar($request,$response);//Transacciones
});
$app->post('/usuario/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
	return usuario::actualizar($request,$response);//Transacciones
});

$app->post('/usuario/actualizarContrasena',function(ServerRequestInterface $request,ResponseInterface $response){
	return usuario::actualizarContrasena($request,$response);//Transacciones
});
$app->post('/usuario/obtenerNuevo',function(ServerRequestInterface $request,ResponseInterface $response){
	return usuario::obtenerNuevo($request,$response);
});
$app->post('/exportar/usuario',function(ServerRequestInterface $request,ResponseInterface $response){
	return exportar::usuario($request,$response);

});
$app->post('/usuario/login', function (ServerRequestInterface $request, ResponseInterface $response){
	return usuario::logIn($request,$response);
});
$app->post('/usuario/permisos/obtener/{id}', function (ServerRequestInterface $request, ResponseInterface $response){
	$route = $request->getAttribute('route');
	$courseId = $route->getArgument('id');
	$newResponse = $response->withJson(PrivilegiosUsuario::obtenerPorUsuario($courseId),200);
	return ($newResponse);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Sucursal Catch
$app->post('/sucursal/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return sucursal::seleccionar($request,$response);
});
$app->post('/sucursal/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
	return sucursal::registrar($request,$response);//Transacciones
});
$app->post('/sucursal/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
	return sucursal::actualizar($request,$response);//Transacciones
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Permisos Catch
$app->post('/permisos/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return $response->withJson(permisos::seleccionar($request,$response),200);
});
$app->post('/permisos/actualizar', function (ServerRequestInterface $request, ResponseInterface $response){
	return permisos::actualizar($request,$response);//Transacciones
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Departamento Catch
$app->post('/departamento/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return departamento::seleccionar($request,$response);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Categoria Catch
$app->post('/categoria/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return categoria::seleccionar($request,$response);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Artículo Catch
$app->post('/articulo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return articulo::seleccionar($request,$response);
});
$app->post('/articulo/seleccionarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
	return articulo::seleccionarListaFija($request,$response);
});
$app->post('/articulo/impuesto/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
	return articulo::insertar($request,$response);//Transacciones
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Inventario Catch
$app->post('/inventario/seleccionarAzar', function (ServerRequestInterface $request, ResponseInterface $response){
	return inventario::seleccionarAzar($request,$response);
});
$app->post('/inventario/seleccionarIndividual', function (ServerRequestInterface $request, ResponseInterface $response){
	return inventario::seleccionarIndividual($request,$response);
});
$app->post('/inventario/seleccionarMasConflictivos', function (ServerRequestInterface $request, ResponseInterface $response){
	return inventario::seleccionarMasConflictivos($request,$response);
});
$app->post('/inventario/seleccionarMasVendidos', function (ServerRequestInterface $request, ResponseInterface $response){
	return inventario::seleccionarMasVendidos($request,$response);
});
$app->post('/inventario/insertar', function (ServerRequestInterface $request, ResponseInterface $response){
	return inventario::insertar($request,$response);//Transacciones
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Bitacora Catch
$app->post('/bitacora/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return bitacora::seleccionar($request,$response);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Parámetros Catch
$app->post('/parametros/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::seleccionar($request,$response);
});
$app->post('/parametros/seleccionarEstado', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::seleccionarEstado($request,$response);
});
$app->post('/parametros/seleccionar/lista/fija', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::seleccionarListaFija($request,$response);
});
$app->post('/parametros/seleccionar/lista/excluyente', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::seleccionarListaFijaExcluyente($request,$response);
});
$app->post('/parametros/seleccionar/lista/fija/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::seleccionarListaFijaInventario($request,$response);
});
$app->post('/parametros/insertarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::insertarListaFija($request,$response);//Transacciones
});
$app->post('/parametros/eliminarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::eliminarListaFija($request,$response);//Transacciones
});
$app->post('/parametros/actualizar',function(ServerRequestInterface $request,ResponseInterface $response){
	return parametros::actualizar($request,$response);//Transacciones
});
$app->post('/parametros/actualizarListaFija', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::actualizarListaFija($request,$response);//Transacciones
});
$app->post('/parametros/actualizarListaExcluyente', function (ServerRequestInterface $request, ResponseInterface $response){
	return parametros::actualizarListaExcluyente($request,$response);//Transacciones
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Sexo Catch
$app->post('/sexo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return sexo::seleccionar($request,$response);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Nivel de Autorización Catch
$app->post('/nivel_autorizacion/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return nivel_autorizacion::seleccionar($request,$response);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Batch catch
$app->post('/importar/departamento', function (ServerRequestInterface $request, ResponseInterface $response){
	return importar::Departamento($request,$response);//Transacciones
});
$app->post('/importar/categoria', function (ServerRequestInterface $request, ResponseInterface $response){
	return importar::Categoria($request,$response);//Transacciones
});
$app->post('/importar/articulo', function (ServerRequestInterface $request, ResponseInterface $response){
	return importar::Articulo($request,$response);//Transacciones
});

$app->post('/exportar/parametro', function (ServerRequestInterface $request, ResponseInterface $response){
	return exportar::Parametro($request,$response);

});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Concepto Catch
$app->post('/concepto/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response){
	return concepto::seleccionar($request,$response);
});
$app->post('/sucursal/login', function (ServerRequestInterface $request, ResponseInterface $response){
	return sucursal::login($request,$response);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Catch
$app->post('/exportar/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
	return exportar::exportarInventarioAPI($request,$response);
});
$app->post('/exportar/inventario/actualizar', function (ServerRequestInterface $request, ResponseInterface $response){
	return exportar::actualizarInventario($request,$response);//Transacciones
});
$app->post('/importar/inventario', function (ServerRequestInterface $request, ResponseInterface $response){
	return importar::importarInventarioAPI($request,$response);//Transacciones
});
$app->post('/exportar/venta/obtener/ultima',function(ServerRequestInterface $request,ResponseInterface $response){
	return exportar::ultimaVenta($request,$response);
});

$app->post('/importar/venta',function(ServerRequestInterface $request,ResponseInterface $response){
	return importar::venta($request,$response);//Transacciones
});

$app->post('/importar/venta/detalle',function(ServerRequestInterface $request,ResponseInterface $response){
	return importar::detalleventa($request,$response);//Transacciones
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////// Catch
$app->post('/tipo_pago/insertar',function(ServerRequestInterface $request,ResponseInterface $response){
	return tipo_pago::insertar($request,$response);//Transacciones
});
/////////////////// Catch
$app->post('/detalles_venta/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response){
	return detalles_venta::seleccionar($request,$response);
});
/////////////////
$app->run();

function getConnection1() {
	$dbhost="192.168.1.185";
	$dbuser="root";
	$dbpass="sysadmin";
	$dbname="sicar";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}
function getConnection() {
	$dbhost="50.62.209.162";
	$dbuser="mercattoadmin";
	$dbpass="Sy5@dm1n1*";
	$dbname="mercatto_mstock";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;

}
