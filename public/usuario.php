<?php

class usuario
{
    public function __construct(){
    }

    public static function seleccionar($request,$response){
        $comando = "SELECT mu.idUsuario,mu.usuario,'DEFAULTMERCASTOCK' as password,mu.nombre,mu.apellido,mu.idNivelAutorizacion,mu.idSucursal,mss.nombre as descripcionSucursal,mu.sexo,mu.idEstado,mu.contacto,mn.descripcion
                    FROM ms_usuario mu INNER JOIN ms_nivelAutorizacion mn ON (mn.idNivelAutorizacion = mu.idNivelAutorizacion)
                                       inner join ms_sucursal mss on (mss.idSucursal = mu.idSucursal)
        WHERE mu.idNivelAutorizacion>1";// mayor que superadmin
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            $arreglo=null;
            if ($resultado) {

                return $response->withJson($resultado, 200);
            } else {
                $arreglo = [
                    "estado" => "warning",
                    "success" => "Error al traer listado de usuarios ya que no existen",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
            //return $response->withJson($arreglo, 400);
        }
    }

    public static function insertar($request, $response)
    {
        $claveApi = "";
        $idEstado = "A";
        $claveGCM = '0';
        $idUsuario = self::obtenerIdUsuario();
        $postrequest = json_decode($request->getBody());
        $sql = "INSERT INTO ms_usuario (idUsuario,usuario,password,nombre,apellido,sexo,contacto,idSucursal,claveAPI,idEstado,fechaEstado,fechaSesion,claveGCM,idNivelAutorizacion) VALUES
                (:idUsuario,:usuario,:password,:nombre,:apellido,:sexo,:contacto,:idSucursal,:claveAPI,:idEstado,now(),now(),:claveGCM,:idNivelAutorizacion)";
        $db=null;
        try {
            $password = self::encriptarContrasena($postrequest->password);
            $db = getConnection();
            $db->beginTransaction();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(":usuario", $postrequest->usuario, PDO::PARAM_STR);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":nombre", $postrequest->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $postrequest->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":sexo", $postrequest->sexo, PDO::PARAM_STR);
            $stmt->bindParam(":contacto", $postrequest->contacto, PDO::PARAM_INT);
            $stmt->bindParam(":idSucursal", $postrequest->idSucursal, PDO::PARAM_INT);
            $stmt->bindParam(":claveAPI", $claveApi);
            $stmt->bindParam(":idEstado", $idEstado);
            $stmt->bindParam(":claveGCM", $claveGCM);
            $stmt->bindParam(":idNivelAutorizacion", $postrequest->idNivelAutorizacion, PDO::PARAM_INT);
            $stmt->execute();
            $postrequest->id = $db->lastInsertId();
            if ($postrequest->idUsuario > 0) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "Usuario " . $postrequest->usuario . " registrado correctamente",
                    "datos" => $postrequest
                ];
                $db->commit();
                $codigo = 200;

            } else {
                $db->rollBack();
                $arreglo = [
                    "estado" => 400,
                    "error" => "transaccion sin terminar",
                    "datos" => $postrequest
                ];;
                $codigo = 400;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => ($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    private static function obtenerIdUsuario()
    {
        $sql = "SELECT max(idusuario)+1 AS idUsuario FROM ms_usuario";
        try {
            $db = getConnection();
            $query = $db->prepare($sql);
            $query->execute();
            $resultado = $query->fetchObject();
            return $resultado->idUsuario;
        } catch (PDOException $e) {
            return null;
        }
        finally{
            $db=null;
            //return null;
        }
    }

    public static function actualizar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        if($postrequest->password!='DEFAULTMERCASTOCK'){
            $query = "UPDATE ms_usuario SET password=:password,nombre=:nombre,apellido=:apellido,sexo=:sexo,contacto=:contacto," .
                "idSucursal=:idSucursal,idEstado=:idEstado,idNivelAutorizacion=:idNivelAutorizacion WHERE usuario=:usuario";
        }else{
            $query = "UPDATE ms_usuario SET nombre=:nombre,apellido=:apellido,sexo=:sexo,contacto=:contacto," .
                "idSucursal=:idSucursal,idEstado=:idEstado,idNivelAutorizacion=:idNivelAutorizacion WHERE usuario=:usuario";
        }
        try {
            $db = getConnection();
            $db->beginTransaction();
            $password = self::encriptarContrasena($postrequest->password);
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("usuario", $postrequest->usuario);
            if($postrequest->password!='DEFAULTMERCASTOCK'){
                $sentencia->bindParam("password", $password);
            }
            $sentencia->bindParam("nombre", $postrequest->nombre);
            $sentencia->bindParam("apellido", $postrequest->apellido);
            $sentencia->bindParam("sexo", $postrequest->sexo);
            $sentencia->bindParam("contacto", $postrequest->contacto);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("idEstado", $postrequest->idEstado);
            $sentencia->bindParam("idNivelAutorizacion", $postrequest->idNivelAutorizacion);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
                        "data" => $resultado
                    ];
                $db->commit();
                $codigo=200;
            } else {
                $db->rollBack();
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "No se cambió ningún dato",
                        "data" => $resultado
                    ];
                $codigo=200;
            }
        } catch (PDOException $e) {
            $db->rollBack();

            $arreglo = [
                "estado" => $e->getCode(),
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function actualizarContrasena($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $usuario=$postrequest->usuario;
        $db=null;
        $comando = "SELECT idUsuario,Usuario,password,claveAPI,IDESTADO,idNivelAutorizacion FROM ms_usuario WHERE usuario=:usuario AND idSucursal=0";
        try {
            $db = getConnection();
            $db->beginTransaction();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("usuario", $usuario);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetchObject();
                if ($resultado) {
                    $query2 = "UPDATE ms_usuario SET password=:passwordNueva WHERE usuario=:usuario";
                    try {
                        $autenticar = self::autenticar($postrequest->usuario, $postrequest->passwordActual);

                        if ($autenticar['estado'] == '200') {
                            //$db = getConnection();
                            $passwordNueva = self::encriptarContrasena($postrequest->passwordNueva);
                            $sentencia = $db->prepare($query2);
                            $sentencia->bindParam("passwordNueva", $passwordNueva);
                            $sentencia->bindParam("usuario", $postrequest->usuario);
                            $resultado = $sentencia->execute();
                            if ($resultado) {
                                $arreglo =
                                    [
                                        "estado" => "success",
                                        "success" => "Se ha actualizado la contraseña con éxito",
                                        "data" => $resultado
                                    ];
                                $db->commit();
                                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                            } else {
                                $db->rollBack();
                                $arreglo =
                                    [
                                        "estado" => "warning",
                                        "mensaje" => "No se cambió ningún dato",
                                        "data" => $resultado
                                    ];
                                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                            }
                        } else {
                            $db->rollBack();
                            $arreglo =
                                [
                                    "estado" => "warning",
                                    "mensaje" => "La contraseña actual no es correcta",
                                    "data" => "Error"
                                ];
                            return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                        }
                    } catch (PDOException $e) {
                        $db->rollBack();
                        $arreglo =
                            [
                                "estado" => "warning",
                                "mensaje" => "La contraseña actual no es correcta",
                                "data" => general::traducirMensaje($e->getCode(),$e)
                            ];
                        return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                    } finally {
                        $db = null;
                    }
                } else {
                    return false;
                }
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo =
                [
                    "estado" => "warning",
                    "mensaje" => "La contraseña actual no es correcta",
                    "data" => general::traducirMensaje($e->getCode(),$e)
                ];
            return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
        }
    }



    public static function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else return null;
    }

    public static function logIn($request, $response)
    {
        $usuario = json_decode($request->getBody());
        $correo = $usuario->usuario;
        $contrasena = $usuario->contrasena;
        try {
            $autenticar = self::autenticar($correo, $contrasena);
            if ($autenticar['estado'] == '200') {
                $codigo = 200;

            } else {
                $codigo = 401;
            }
            return $response->withJson($autenticar, $codigo);
        } catch (PDOException $e) {
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($usuario)
            ];
            return $response->withJson($arreglo,400);
        }
        finally{
            $db=null;
        }
    }

    private static function autenticar($usuario, $contrasena)
    {
        $comando = "SELECT idUsuario,Usuario,password,claveAPI,IDESTADO,idNivelAutorizacion FROM ms_usuario WHERE usuario=:usuario AND idSucursal=1";
       // var_dump($comando);
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("usuario", $usuario);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetchObject();
                if (($resultado) && self::validarContrasena($contrasena, $resultado->password)) {
                    self::actualizarSesion($usuario);
                    try {
                        return
                            [
                                "estado" => 200,
                                "mensaje" => "OK",
                                "datos" => ["idUsuario"=>$resultado->idUsuario,"Usuario"=>$resultado->Usuario,"ClaveAPI"=>self::$claveApi
                                    ,"IDESTADO"=>$resultado->IDESTADO,"idNivelAutorizacion"=>$resultado->idNivelAutorizacion]
                            ];
                    } catch (PDOException $e) {
                        $codigoDeError=$e->getCode();
                        $error =general::traducirMensaje($codigoDeError,$e);
                        $arreglo = [
                            "estado" =>$e -> getCode(),
                            "error" =>$error,
                            "data" => json_encode($usuario)
                        ];
                        return $arreglo;
                    }
                } else {
                    return
                        [
                            "estado" => 101,
                            "mensaje" => "usuario inexistente"
                        ];
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $db=null;
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($usuario)
            ];
            return $arreglo;
        }
    }

    private static function validarContrasena($contrasenaPlana, $contrasenaHash)
    {
        return password_verify($contrasenaPlana, $contrasenaHash);
    }
    public static $claveApi;
    private static function actualizarSesion($usuario)
    {
        $comando2 = "UPDATE ms_usuario SET fechaSesion=NOW(),claveAPI=:claveApi WHERE usuario=:usuario";
        try {
            $db = getConnection();
            self::$claveApi = self::generarClaveApi();
            $sentencia2 = $db->prepare($comando2);
            $sentencia2->bindParam("usuario", $usuario);
            $sentencia2->bindParam("claveApi", self::$claveApi);
            if ($sentencia2->execute()) {
                return true;
            } else
                return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function generarClaveApi()
    {
        return md5(microtime() . rand());
    }

    public static function revisarToken($token){
        $query = "select claveAPI from ms_sucursal where claveAPI=:claveApi and claveAPI!='' union all select claveAPI from ms_usuario where claveAPI=:claveApi
                  and claveAPI!=''";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("claveApi",$token);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if($resultado){
                return true;
            } else {
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }
    public static function seleccionarApi($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        //$codigo=200;
       // var_dump($postrequest);
        $arreglo=[];

        $comando = "SELECT usuario FROM ms_usuario WHERE usuario=:usuario AND claveAPI=:claveApi";

        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('usuario',$postrequest->datos->Usuario);
            $sentencia->bindParam('claveApi',$postrequest->datos->ClaveAPI);
            $sentencia->execute();
            $resultado = $sentencia->fetchObject();

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
              return  $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "success" => "Error al autenticarz",
                    "data" => $postrequest->datos
                ];;
                return  $response->withJson($arreglo, 400, JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e
            ];
            return  $response->withJson($arreglo, 400, JSON_UNESCAPED_UNICODE);

        }
        finally{
            $db=null;
            //return $response->withJson($arreglo, $codigo);
        }
    }
}