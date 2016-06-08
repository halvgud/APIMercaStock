<?php

class sucursal
{
    protected function __construct(){
    }
    public static function seleccionar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $bandera = isset($postrequest->idGenerico->bandera)?true:false;
        $codigo=200;
        $arreglo=[];
        if(!isset($postrequest->banderaSucursal)) {
            if ($bandera) {
                $comando = "SELECT idSucursal,nombre,usuario, domicilio, contacto, idEstado,'DEFAULTMERCASTOCK' AS password FROM ms_sucursal";
            } else {
                $comando = "SELECT idSucursal,nombre,usuario, domicilio, contacto, idEstado,'DEFAULTMERCASTOCK' AS password FROM ms_sucursal WHERE idEstado>0";
            }
        }else{
            $comando = "SELECT idSucursal,nombre,usuario, domicilio, contacto, idEstado,'DEFAULTMERCASTOCK' AS password FROM ms_sucursal";
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
               $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Sucursal",
                    "data" => $resultado
                ];;
               $codigo=400;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Sucursal",
                "data" => $e
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function registrar($request, $response)
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
            //$db = null;
            if ($wine->id > 1) {

                $sql1="insert into ms_parametro values(
                    '1', 'CONFIG_GENERAR_INVENTARIO', 'BANDERA_LISTA_FIJA_SUC', $wine->id, 'TRUE', 'JCDL', NOW()
                );";
                $sql2="insert into ms_parametro values(
                    '1', 'CONFIG_GENERAR_INVENTARIO', 'BANDERA_LISTA_EXCLUYENTE_SUC', $wine->id, 'TRUE', 'JCDL', NOW()
                );";
                $stmt2 = $db->prepare($sql1);

                $stmt3 = $db->prepare($sql2);

                $stmt2->bindParam("idSucursal", $wine->idSucursal, PDO::PARAM_STR);
                $stmt3->bindParam("idSucursal", $wine->idSucursal, PDO::PARAM_STR);
                $stmt2->execute();
                $stmt3->execute();
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

    public static function actualizar($request, $response)
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
    private static function validarContrasena($contrasenaPlana, $contrasenaHash)
    {
        return password_verify($contrasenaPlana, $contrasenaHash);
    }
    private static function generarClaveApi()
    {
        return md5(microtime() . rand());
    }
    public static $claveNuevaAPI="";
    private static function actualizarSesion($usuario,$idSucursal)
    {
        $comando2 = "UPDATE ms_sucursal SET claveAPI=:claveApi WHERE usuario=:usuario and idSucursal=:idSucursal";
        try {
            $db = getConnection();
            $claveApi = self::generarClaveApi();
            self::$claveNuevaAPI=$claveApi;
            $sentencia2 = $db->prepare($comando2);
            $sentencia2->bindParam("usuario", $usuario);
            $sentencia2->bindParam("idSucursal",$idSucursal);
            $sentencia2->bindParam("claveApi", $claveApi);
            if ($sentencia2->execute()) {
                return true;
            } else
                return false;
        } catch (PDOException $e) {
            throw new ExcepcionApi(401, $e->getMessage(), 401);
        }
    }

    public static function login2($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "SELECT idSucursal,usuario, claveAPI FROM ms_sucursal where usuario=:usuario and password=:password
        and idSucursal=:idSucursal";
        try{
            $db=getConnection();
            $passwordn=$postrequest[0]->password;
            $passwordn = self::encriptarContrasena($passwordn);
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("usuario",$postrequest[0]->usuario);
            $sentencia->bindParam("password",$passwordn);
            $sentencia->bindParam("idSucursal",$postrequest[0]->idSucursal);

            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if($resultado){
                 $arreglo =
                            [
                                "estado" => 200,
                                "success" => "",
                                "data" => $resultado
                            ];
                        return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                    } else {
                        $arreglo =
                            [
                                "estado" => "warning",
                                "mensaje" => "",
                                "data" => $passwordn
                            ];
                        return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                      }
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =self::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo,400);
        }
    }


    public static function logIn($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $usuario = isset($postrequest->usuario)?$postrequest->usuario:"";
        $password = isset($postrequest->usuario)?$postrequest->password:"";
        $idSucursal = isset($postrequest->idSucursal)?$postrequest->idSucursal:"";
        try {
            $autenticar = self::autenticar($usuario, $password,$idSucursal,$postrequest);
            if ($autenticar['estado'] == '200') {
                $datos = $autenticar['datos'];
                $codigo = 200;

            } else {
                $datos=$autenticar;
                $codigo = 401;
            }
            return $response->withJson($datos, $codigo);;
        } catch (PDOException $e) {
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
        finally{
            $db=null;
        }
    }

    private static function autenticar($usuario, $contrasena,$idSucursal,$postrequest)
    {
        $comando = "SELECT idSucursal,password,usuario, claveAPI,nombre FROM ms_sucursal where usuario=:usuario and idSucursal=:idSucursal";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("usuario", $usuario);
            $sentencia->bindParam("idSucursal",$idSucursal);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetchObject();
                if (($resultado) && self::validarContrasena($contrasena, $resultado->password)) {
                    self::actualizarSesion($usuario,$resultado->idSucursal);
                    try {
                        return
                            [
                                "estado" => 200,
                                "mensaje" => "OK",
                                "datos" => ["idSucursal"=>$resultado->idSucursal,"claveAPI"=>self::$claveNuevaAPI,
                                            "password"=>$resultado->password,"usuario"=>$resultado->usuario,
                                            "nombre"=>$resultado->nombre]
                            ];
                    } catch (PDOException $e) {
                        throw new ExcepcionApi(2, $e->getMessage());
                    }
                } else {
                    return
                        [
                            "estado" => 101,
                            "mensaje" => "usuario inexistente",
                            "data"=>$postrequest
                        ];
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(1, $e->getMessage(), 401);
        }
    }
}