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

$app->post('/usuario/login', function (ServerRequestInterface $request, ResponseInterface $response, $args){
        return logIn($request,$response,$args);
    }); 

$app->run();

function logIn($request,$response,$args){
	$usuario = json_decode($request->getBody());
	$correo = $usuario->usuario;
	$contrasena = $usuario->contrasena;
try {
	$autenticar = autenticar($correo,$contrasena);
	if ($autenticar['estado']=='200') {
		
		$_SESSION['id_usuario']= 'id_usuario';
		$_SESSION['usuario'] = 'usuario';
		$_SESSION['rol'] = 'rol';
		$resArray['success'] = 'Se ha logueado correctamente';
                $codigo=200;
        }else{
            $codigo=401;
	}
                $newResponse = $response->withJson($autenticar,$codigo);
		return $newResponse;
	
        
	/*
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario", $usuario->usuario);
		$stmt->bindParam("contrasena", $usuario->contrasena);
		$stmt->execute();
		$wine = $stmt->fetchObject();
		$db = null;
		echo json_encode($wine);*/
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}
function actualizarSesion($usuario){
	$comando2 = "UPDATE ms_usuario SET fechaSesion=NOW(),claveAPI=:claveApi WHERE usuario=:usuario";

	try {
		$db = getConnection();
		$claveApi = generarClaveApi();
		$sentencia2 = $db->prepare($comando2);
		$sentencia2->bindParam("usuario",$usuario);
		$sentencia2->bindParam("claveApi",$claveApi);
		if ($sentencia2->execute()) {
			return true;
		} else
			return false;
	} catch (PDOException $e) {
		throw new ExcepcionApi(401, $e->getMessage(),401);
	}
}
function generarClaveApi()
{
	return md5(microtime() . rand());
}
function validarContrasena($contrasenaPlana, $contrasenaHash)
{
	//        return var_dump(password_verify($contrasenaPlana, $contrasenaHash));
	return password_verify($contrasenaPlana, $contrasenaHash);
}

function autenticar($usuario, $contrasena)
{
	$comando = "SELECT idUsuario,password,IDESTADO,idNivelAutorizacion FROM ms_usuario WHERE usuario=:usuario ";

	try {
		$db = getConnection();
		$sentencia = $db->prepare($comando);
		$sentencia->bindParam("usuario", $usuario);
		$sentencia->execute();

		//$db = null;
		if ($sentencia) {
			$resultado = $sentencia->fetchObject();
				if(($resultado)&&validarContrasena($contrasena, $resultado->password)){
					actualizarSesion($usuario);
			try {
					//if ($sentencia->execute()) {
						
						return
								[
										"estado" => 200,
                                                                                "mensaje"=>"OK",
										"datos" => $resultado
								];
					//} else
					//	throw new ExcepcionApi("", "Se ha producido un error");

				} catch (PDOException $e) {
					throw new ExcepcionApi(2, $e->getMessage());
				}
			}else {
				
					return
							[
									"estado" => 101,
									"mensaje"=>"usuario inexistente"
							];
			}

		} else {
			return false;
		}
	} catch (PDOException $e) {
		throw new ExcepcionApi(1, $e->getMessage(),401);
	}

}




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
?>

