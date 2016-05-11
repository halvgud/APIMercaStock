<?php
class usuario
{
    protected function __construct()
    {

    }

    public static function seleccionar($request, $response, $args)
    {
        $comando = "SELECT mu.idUsuario,mu.usuario,'DEFAULTMERCASTOCK' as password,mu.nombre,mu.apellido,mu.idNivelAutorizacion,mu.idSucursal,mu.sexo,mu.idEstado,mu.contacto,mn.descripcion FROM ms_usuario mu INNER JOIN ms_nivelAutorizacion mn ON (mn.idNivelAutorizacion = mu.idNivelAutorizacion)
        WHERE mu.idNivelAutorizacion>0";// mayor que superadmin
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //$sentencia->bindParam("idUsuario",$idUsuario, PDO::PARAM_STR);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                return $response->withJson($resultado, 200);
            } else {
                $arreglo = [
                    "estado" => "warning",
                    "success" => "Error al traer listado de usuarios ya que no existen",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Sexo",
                "data" => $e
            ];
            return $response->withJson($arreglo, 400);
        }
    }

    public static function insertar($request, $response, $args)
    {
        $wine = json_decode($request->getBody());

        $claveApi = "";
        $idEstado = "A";
        $claveGCM = '0';
        $idUsuario = self::obtenerIdUsuario();

        $sql = "INSERT INTO ms_usuario (idUsuario,usuario,password,nombre,apellido,sexo,contacto,idSucursal,claveAPI,idEstado,fechaEstado,fechaSesion,claveGCM,idNivelAutorizacion) VALUES
	            (:idUsuario,:usuario,:password,:nombre,:apellido,:sexo,:contacto,:idSucursal,:claveAPI,:idEstado,now(),now(),:claveGCM,:idNivelAutorizacion)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("idUsuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(":usuario", $wine->usuario, PDO::PARAM_STR);
            $stmt->bindParam(":password", $wine->password, PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $wine->nombre, PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $wine->apellido, PDO::PARAM_STR);
            $stmt->bindParam(":sexo", $wine->sexo, PDO::PARAM_STR);
            $stmt->bindParam(":contacto", $wine->contacto, PDO::PARAM_INT);
            $stmt->bindParam(":idSucursal", $claveGCM, PDO::PARAM_INT);
            $stmt->bindParam(":claveAPI", $claveApi);
            $stmt->bindParam(":idEstado", $idEstado);
            $stmt->bindParam(":claveGCM", $claveGCM);
            $stmt->bindParam(":idNivelAutorizacion", $wine->idNivelAutorizacion, PDO::PARAM_INT);
            $stmt->execute();
            $wine->id = $db->lastInsertId();
            $db = null;
            if ($wine->idUsuario > 0) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "Usuario " . $wine->usuario . " registrado correctamente",
                    "datos" => $wine
                ];
                return $response->withJson($arreglo, 200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "transaccion sin terminar",
                    "datos" => $wine
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "datos" => json_encode($wine)
            ];;
            return $response->withJson($arreglo, 400);
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

        }

    }

    public static function actualizar($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        if($postrequest->password!='DEFAULTMERCASTOCK'){
            $query = "UPDATE ms_usuario SET password=:password,nombre=:nombre,apellido=:apellido,sexo=:sexo,contacto=:contacto," .
                "idSucursal=:idSucursal,idEstado=:idEstado,idNivelAutorizacion=:idNivelAutorizacion WHERE usuario=:usuario";
        }else{
            $query = "UPDATE ms_usuario SET nombre=:nombre,apellido=:apellido,sexo=:sexo,contacto=:contacto," .
                "idSucursal=:idSucursal,idEstado=:idEstado,idNivelAutorizacion=:idNivelAutorizacion WHERE usuario=:usuario";
        }
        try {
            $db = getConnection();
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
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }
    }

    public static function obtener($request,$response,$args){
        $postrequest = json_decode($request->getBody());
        $query = "select idUsuario,usuario,password,nombre,apellido,sexo,contacto,idNivelAutorizacion,idEstado,fechaEstado,fechaSesion,claveGCM from ms_usuario";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            //sentencia->bindParam("",$postrequest->algunargumento);
            $sentencia->execute();
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
                                "data" => $resultado
                            ];
                        return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
                      }//else
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =LogIn::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo,400);
        }

    }
}