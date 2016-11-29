<?php session_start();
if (PHP_SAPI == 'cli-server') {
	// To help the built-in PHP dev server, check if the request was actually for
	// something which should probably be served as a static file
	$file = __DIR__ . $_SERVER['REQUEST_URI'];
	if (is_file($file)) {
		return false;
	}
}

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
require_once("perdidas.php");
require_once("ajuste.php");
require_once("proveedor.php");
//require_once("tipo_pago.php");
require_once("inventarioTemporal.php");
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
	$dbhost="198.71.241.19";
	$dbuser="mercattoadmin";
	$dbpass="Sy5@dm1n1*";
	$dbname="mercatto_mstock";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;

}
