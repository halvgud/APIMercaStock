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
require __DIR__ . '/../src/routes.php';
require_once("usuario.php");
require_once("sucursal.php");
require_once("LogIn.php");
require_once("MercaStock.php");
require_once("departamento.php");
require_once("categoria.php");
require_once("articulo.php");
require_once("bitacora.php");
require_once("inventario.php");
require_once ("usuario2.php");
//require_once "MercaStock.php";
require_once "Roles.php";
require_once "PrivilegiosUsuario.php";
require_once "permisos.php";
require_once("parametros.php");
require_once("nivel_autorizacion.php");
require_once("sexo.php");
require_once("importar.php");
require_once("concepto.php");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Usuario
$app->post('/usuario/seleccionar',function(ServerRequestInterface $request,ResponseInterface $response,$args){
	return usuario::seleccionar($request,$response,$args);
});
$app->post('/usuario/insertar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return usuario::insertar($request,$response,$args);
});
$app->post('/usuario/actualizar',function(ServerRequestInterface $request,ResponseInterface $response,$args){
	return usuario::actualizar($request,$response,$args);
});

$app->post('/usuario/obtenerNuevo',function(ServerRequestInterface $request,ResponseInterface $response,$args){
	return usuario::obtenerNuevo($request,$response,$args);
});
$app->post('/usuario/login', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return usuario::logIn($request,$response,$args);
});
$app->post('/usuario/permisos/obtener/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	$route = $request->getAttribute('route');
	$courseId = $route->getArgument('id');
	$newResponse = $response->withJson(PrivilegiosUsuario::obtenerPorUsuario($courseId),200);
	return ($newResponse);

});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Sucursal
$app->post('/sucursal/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return sucursal::seleccionar($request,$response,$args);
});
$app->post('/sucursal/insertar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return sucursal::registrar($request,$response,$args);
});
$app->post('/sucursal/actualizar',function(ServerRequestInterface $request,ResponseInterface $response,$args){
	return sucursal::actualizar($request,$response,$args);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Permisos
$app->post('/permisos/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return $response->withJson(permisos::seleccionar($request,$response,$args),200);
});
$app->post('/permisos/actualizar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return permisos::actualizar($request,$response,$args);
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Departamento
$app->post('/departamento/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return departamento::seleccionar($request,$response,$args);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Categoria
$app->post('/categoria/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return categoria::seleccionar($request,$response,$args);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Artículo
$app->post('/articulo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return articulo::seleccionar($request,$response,$args);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Inventario
$app->post('/inventario/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return inventario::seleccionar($request,$response,$args);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Bitacora
$app->post('/bitacora/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return bitacora::seleccionar($request,$response,$args);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Parámetros
$app->post('/parametros/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return parametros::seleccionar($request,$response,$args);
});
$app->post('/parametros/actualizar',function(ServerRequestInterface $request,ResponseInterface $response,$args){
	return parametros::actualizar($request,$response,$args);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Sexo
$app->post('/sexo/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return sexo::seleccionar($request,$response,$args);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Nivel de Autorización
$app->post('/nivel_autorizacion/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return nivel_autorizacion::seleccionar($request,$response,$args);
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Batch
$app->post('/importar/departamento', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return importar::modificarEnBatch($request,$response,$args);
});
$app->post('/importar/categoria', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return importar::modificarEnBatch2($request,$response,$args);
});
$app->post('/importar/articulo', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return importar::modificarEnBatch3($request,$response,$args);
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////Concepto
$app->post('/concepto/seleccionar', function (ServerRequestInterface $request, ResponseInterface $response, $args){
	return concepto::seleccionar($request,$response,$args);
});




$app->run();




/*function getWines() {
	$sql = "select * FROM wine ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"wine": ' . json_encode($wines) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function getWine($id) {
	$sql = "SELECT * FROM wine WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$wine = $stmt->fetchObject();
		$db = null;
		echo json_encode($wine);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function addWine() {
	error_log('addWine\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$wine = json_decode($request->getBody());
	$sql = "INSERT INTO wine (name, grapes, country, region, year, description) VALUES (:name, :grapes, :country, :region, :year, :description)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $wine->name);
		$stmt->bindParam("grapes", $wine->grapes);
		$stmt->bindParam("country", $wine->country);
		$stmt->bindParam("region", $wine->region);
		$stmt->bindParam("year", $wine->year);
		$stmt->bindParam("description", $wine->description);
		$stmt->execute();
		$wine->id = $db->lastInsertId();
		$db = null;
		echo json_encode($wine);
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function updateWine($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$wine = json_decode($body);
	$sql = "UPDATE wine SET name=:name, grapes=:grapes, country=:country, region=:region, year=:year, description=:description WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $wine->name);
		$stmt->bindParam("grapes", $wine->grapes);
		$stmt->bindParam("country", $wine->country);
		$stmt->bindParam("region", $wine->region);
		$stmt->bindParam("year", $wine->year);
		$stmt->bindParam("description", $wine->description);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($wine);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function deleteWine($id) {
	$sql = "DELETE FROM wine WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function findByName($query) {
	$sql = "SELECT * FROM wine WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"wine": ' . json_encode($wines) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}
*/
function getConnection() {
	$dbhost="50.62.209.162";
	$dbuser="mercattoadmin";
	$dbpass="Sy5@dm1n1*";
	$dbname="mercatto_mstock";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

