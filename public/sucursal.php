<?php
class sucursal
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        $bandera = isset($postrequest->idGenerico->bandera)?true:false;
        if($bandera){
            $comando = "SELECT idSucursal,nombre,usuario, domicilio, contacto, idEstado,'DEFAULTMERCASTOCK' AS password FROM ms_sucursal";
        }else{
            $comando = "SELECT idSucursal,nombre,usuario, domicilio, contacto, idEstado,'DEFAULTMERCASTOCK' AS password FROM ms_sucursal where idEstado>0";
        }
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Sucursal",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Sucursal",
                "data" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

    public static function registrar($request, $response, $args)
    {
        $wine = json_decode($request->getBody());
        $password = self::encriptarContrasena($wine->password);
        $sql = "INSERT INTO ms_sucursal (idSucursal, nombre, usuario, password, claveAPI, domicilio, contacto, idEstado) VALUES
	    (:idSucursal,:nombre,:usuario,:password,:claveAPI,:domicilio,:contacto,:idEstado)";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("idSucursal", $wine->idSucursal, PDO::PARAM_STR);
            $stmt->bindParam("nombre", $wine->nombre, PDO::PARAM_STR);
            $stmt->bindParam("usuario", $wine->usuario, PDO::PARAM_STR);
            $stmt->bindParam("password", $password, PDO::PARAM_STR);
            $stmt->bindParam("claveAPI", $wine->claveAPI,PDO::PARAM_STR);
            $stmt->bindParam("domicilio", $wine->domicilio, PDO::PARAM_STR);
            $stmt->bindParam("contacto", $wine->contacto, PDO::PARAM_STR);
            $stmt->bindParam("idEstado", $wine->idEstado, PDO::PARAM_STR);
            $stmt->execute();
            $wine->id = $db->lastInsertId();
            $db = null;
            if ($wine->id > 1) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "transaccion terminada",
                    "data" => $wine
                ];
                return $response->withJson($arreglo, 200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "transaccion sin terminar",
                    "data" => $wine
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            echo '{"error":' . $e->getMessage() . '}';
        }
        finally{
            $db=null;
        }
    }

    public static function actualizar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        if($postrequest->password!='DEFAULTMERCASTOCK') {
            $query = "UPDATE ms_sucursal SET nombre=:nombre,usuario=:usuario,password=:password,domicilio=:domicilio,contacto=:contacto,idEstado=:idEstado WHERE idSucursal=:idSucursal";
        }else{
            $query = "UPDATE ms_sucursal SET nombre=:nombre,usuario=:usuario,domicilio=:domicilio,contacto=:contacto,idEstado=:idEstado WHERE idSucursal=:idSucursal";
        }
        try {
            $db = getConnection();
            $password = self::encriptarContrasena($postrequest->password,PDO::PARAM_STR);
            $sentencia = $db->prepare($query);
            $sentencia ->bindParam("idSucursal",$postrequest->idSucursal,PDO::PARAM_INT);
            $sentencia->bindParam("usuario", $postrequest->usuario,PDO::PARAM_STR);
            if($postrequest->password!='DEFAULTMERCASTOCK'){
                $sentencia->bindParam("password", $password);
            }
            $sentencia->bindParam("nombre", $postrequest->nombre,PDO::PARAM_STR);
            $sentencia->bindParam("domicilio", $postrequest->domicilio,PDO::PARAM_STR);
            $sentencia->bindParam("contacto", $postrequest->contacto,PDO::PARAM_STR);
            $sentencia->bindParam("idEstado", $postrequest->idEstado,PDO::PARAM_INT);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
                        "data" => $resultado
                    ];
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            } else {
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "No se cambió ningún dato",
                        "data" => $resultado
                    ];
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "data" => json_encode($postrequest)
            ];;
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

    public static function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else return null;
    }
}