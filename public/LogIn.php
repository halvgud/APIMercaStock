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
	       //var_dump($wine->id );
	       if($wine->id >1){
		      $arreglo = [
										"estado" => 200,
                                                                                "success"=>"transaccion terminada",
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
	       echo '{"error":'. $e->getMessage() .'}';
	  }
     }

	public static function seleccionarSexo($request,$response,$args){

		$comando = "SELECT idSexo, descripcion from ms_Sexo";
		try {
			$db = getConnection();
			$sentencia = $db->prepare($comando);
			$sentencia->execute();
			$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
			if ($resultado){
				return $response->withJson($resultado,200);
			}else{
				$arreglo = [
						"estado" => 400,
						"error"=>"Error al traer listado",
						"datos" => $resultado
				];;
				return $response->withJson($arreglo,400);
			}
		}catch(PDOException $e){
			$arreglo = [
					"estado" => 400,
					"error"=>"Error al traer listado de Sexo",
					"datos" => $e
			];
			return $response->withJson($arreglo,400);//json_encode($wine);
		}

	}
	public static function seleccionarNivel($request,$response,$args){
		$postrequest = json_decode($request->getBody());
		//return $response->withJson(var_dump($postrequest),400);;
		$idUsuario = $postrequest->idGenerico;
		//var_dump($postrequest);
		$comando = "SELECT idNivelAutorizacion, descripcion from ms_nivelAutorizacion where idNivelAutorizacion>(
						select idNivelAutorizacion from ms_usuario where idUsuario=:idUsuario)";
		try {
			$db = getConnection();
			$sentencia = $db->prepare($comando);
			$sentencia->bindParam("idUsuario",$idUsuario, PDO::PARAM_STR);
			$sentencia->execute();
			$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
			if ($resultado){
				return $response->withJson($resultado,200);
			}else{
				$arreglo = [
						"estado" => 400,
						"error"=>"Error al traer listado",
						"datos" => $resultado
				];;
				return $response->withJson($arreglo,400);
			}
		}catch(PDOException $e){
			$arreglo = [
					"estado" => 400,
					"error"=>"Error al traer listado de Sexo",
					"datos" => $e
			];
			return $response->withJson($arreglo,400);//json_encode($wine);
		}
	}
	public static function seleccionarUsuarios($request,$response,$args){
        $comando = "SELECT mu.idUsuario,mu.usuario,mu.nombre,mu.apellido,mu.idNivelAutorizacion,mu.contacto,mu.sexo,mn.descripcion from ms_usuario mu inner join ms_nivelAutorizacion mn on (mn.idNivelAutorizacion = mu.idNivelAutorizacion)
        where mu.idNivelAutorizacion>0";// mayor que superadmin
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //$sentencia->bindParam("idUsuario",$idUsuario, PDO::PARAM_STR);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado){
                return $response->withJson($resultado,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error"=>"Error al traer listado",
                    "datos" => $resultado
                ];;
                return $response->withJson($arreglo,400);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error"=>"Error al traer listado de Sexo",
                "datos" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }
	}
}
