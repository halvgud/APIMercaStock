<?php
class logIn{
     protected function __construct() {
   
    }
    
public static function logIn($request,$response,$args){
	$usuario = json_decode($request->getBody());
	$correo = $usuario->usuario;
	$contrasena = $usuario->contrasena;
try {
	$autenticar = self::autenticar($correo,$contrasena);
	if ($autenticar['estado']=='200') {
		$datos = $autenticar['datos'];
		$_SESSION['idUsuario']= $datos->idUsuario;
		$_SESSION['usuario'] =$datos->Usuario;
		$_SESSION['idNivelAutorizacion'] =$datos->idNivelAutorizacion;
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
private static function actualizarSesion($usuario){
	$comando2 = "UPDATE ms_usuario SET fechaSesion=NOW(),claveAPI=:claveApi WHERE usuario=:usuario";

	try {
		$db = getConnection();
		$claveApi = self::generarClaveApi();
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
private static function generarClaveApi()
{
	return md5(microtime() . rand());
}
private static function validarContrasena($contrasenaPlana, $contrasenaHash)
{
	//        return var_dump(password_verify($contrasenaPlana, $contrasenaHash));
	return password_verify($contrasenaPlana, $contrasenaHash);
}

private static function autenticar($usuario, $contrasena)
{
	$comando = "SELECT idUsuario,Usuario,password,IDESTADO,idNivelAutorizacion FROM ms_usuario WHERE usuario=:usuario ";

	try {
		$db = getConnection();
		$sentencia = $db->prepare($comando);
		$sentencia->bindParam("usuario", $usuario);
		$sentencia->execute();

		//$db = null;
		if ($sentencia) {
			$resultado = $sentencia->fetchObject();
				if(($resultado)&&self::validarContrasena($contrasena, $resultado->password)){
					self::actualizarSesion($usuario);
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

}
?>