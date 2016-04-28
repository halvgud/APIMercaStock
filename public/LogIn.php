<?php
class logIn{
     const NOMBRE_TABLA = "ms_usuario";
        const ID_USUARIO = "idUsuario";
        const NOMBRE = "nombre";
        const CONTRASENA = "contrasena";
        const USUARIO = "usuario";
        const CLAVE_API = "claveApi";
        //const NOMBRE = "nombre";
        const APELLIDO = "apellido";
        const SEXO = "sexo";
        const ID_SUCURSAL = "idSucursal";
        const CONTACTO = "contacto";
        const ID_ESTADO = "idEstado";
        const FECHA_ESTADO = "fechaEstado";
        const ESTADO_USUARIO_BLOQUEADO=11;
        const ESTADO_CREACION_EXITOSA = 1;
        const ESTADO_CREACION_FALLIDA = 2;
        const ESTADO_ERROR_BD = 3;
        const ESTADO_AUSENCIA_CLAVE_API = 4;
        const ESTADO_CLAVE_NO_AUTORIZADA = 5;
        const ESTADO_URL_INCORRECTA = 6;
        const ESTADO_FALLA_DESCONOCIDA = 7;
        const ESTADO_PARAMETROS_INCORRECTOS = 8;
        const ESTADO_EXITO=9;
        const ID_NIVEL_AUTORIZACION="idNivelAutorizacion";
        const FECHA_SESION = "fechaSesion";
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


public static function registrarUsuario($request,$response,$args)//$datosUsuario
     {
	  //$request = Slim::getInstance()->request();
	  $wine = json_decode($request->getBody());
	  $claveApi="";
	  $idEstado="A";
	  $claveGCM='0';
	  
	 //return  $response->withJson($wine,400);
	  $sql = "INSERT INTO ms_usuario (usuario,password,nombre,apellido,sexo,contacto,idSucursal,claveAPI,idEstado,fechaEstado,fechaSesion,claveGCM,idNivelAutorizacion) VALUES
	  (:usuario,:password,:nombre,:apellido,:sexo,:contacto,:idSucursal,:claveAPI,:idEstado,now(),now(),:claveGCM,:idNivelAutorizacion)";
	  try {/* , grapes, country, region, year, description) VALUES (:name, :grapes, :country, :region, :year, :description)";  */
	       $db = getConnection();	
	       $stmt = $db->prepare($sql);
	       $stmt->bindParam(":usuario",$wine->usuario, PDO::PARAM_STR);
	       $stmt->bindParam(":password",$wine->password, PDO::PARAM_STR);
	       $stmt->bindParam(":nombre",$wine->nombre, PDO::PARAM_STR);
	       $stmt->bindParam(":apellido",$wine->apellido, PDO::PARAM_STR);
	       $stmt->bindParam(":sexo",$wine->sexo, PDO::PARAM_STR);
	       $stmt->bindParam(":contacto",$wine->contacto, PDO::PARAM_INT);
	       $stmt->bindParam(":idSucursal",$claveGCM, PDO::PARAM_INT);
	       $stmt->bindParam(":claveAPI",$claveApi);
	       $stmt->bindParam(":idEstado",$idEstado);
	       $stmt->bindParam(":claveGCM",$claveGCM);
	       $stmt->bindParam(":idNivelAutorizacion",$wine->idNivelAutorizacion, PDO::PARAM_INT);
	       $stmt->execute();
	       $wine->id = $db->lastInsertId();
	       $db = null;
	       var_dump($wine->id );
	       if($wine->id >1){
		      $arreglo = [
										"estado" => 200,
                                                                                "mensaje"=>"transaccion terminada",
										"datos" => $wine
								];
	       return $response->withJson($arreglo,200);//json_encode($wine);
	       }else{
		    $arreglo = [
										"estado" => 400,
                                                                                "error"=>"transaccion sin terminar",
										"datos" => $wine
								];;
		    return $response->withJson($arreglo,400);//json_encode($wine);
	       }
          } catch(PDOException $e) {
              // error_log($e->getMessage(), 3, '/var/tmp/php.log');
	       echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	  }
     }
	/*
	  $json = json_decode($request->getBody());
	//$correo = $usuario->usuario;
	//$contrasena = $usuario->contrasena;
            $post = json_decode(file_get_contents('php://input'),true);
          
            $claveApi2=$post['claveApi2'];
            $usuario=$post['usuario'];
            $contrasena =$post['contrasena'];
            $contrasenaEncriptada = self::encriptarContrasena($contrasena);
            $nombre = $post['nombre'];
            $apellido = $post['apellido'];
            $sexo=$post['sexo'];
            $contacto=$post['contacto'];
            $idsucursal=$post['idSucursal'];
            //$claveApi = self::generarClaveApi();
            $claveApi=$post['claveApi'];
            $idNivelAutorizacion=$post['idNivelAutorizacion'];
            $idEstado = $post['idEstado'];
            $fechaEstado = $post['fechaEstado'];
            $fechaSesion = $post['fechaSesion'];
            try {
    
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
    
                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    //self::ID_USUARIO . ",
                self::USUARIO . ",".
                    self::CONTRASENA . "," .
                    self::NOMBRE . "," .
                    self::APELLIDO . "," .
                    self::SEXO . "," .
                    self::CONTACTO . "," .
                    self::ID_SUCURSAL . "," .
                    self::CLAVE_API . ",".
                    self::ID_NIVEL_AUTORIZACION.",".
                    self::ID_ESTADO.",".
                    self::FECHA_ESTADO .",".
                    self::FECHA_SESION
                    .")" .
                    " VALUES(?,?,?,?,?,?,?,?,?,?,now(),now())";
    
                $sentencia = $pdo->prepare($comando);
    
                //$sentencia->bindParam(1, $idusuario);
                $sentencia->bindParam(1, $usuario);
                $sentencia->bindParam(2, $contrasenaEncriptada);
                $sentencia->bindParam(3, $nombre);
                $sentencia->bindParam(4, $apellido);
                $sentencia->bindParam(5, $sexo);
                $sentencia->bindParam(6, $contacto);
                $sentencia->bindParam(7, $idsucursal);
                $sentencia->bindParam(8, $claveApi);
                $sentencia->bindParam(9,$idNivelAutorizacion);
                $sentencia->bindParam(10,$idEstado);
                //$sentencia->bindParam(12,$fechaEstado);
              // $sentencia->bindParam(13,$fechaSesion);
                if(!self::apiregistro($claveApi2)==null){
                $resultado = $sentencia->execute();
                    if ($resultado) {
                        return self::ESTADO_CREACION_EXITOSA;
                    } else {
                        return self::ESTADO_CREACION_FALLIDA;
                    }
                }
                else{
                     throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA,
                        "Clave Api invalida",401);
                }
                
            } catch (PDOException $e) {
                throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
            }
        }*/
}
