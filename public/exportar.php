<?php
class exportar
{
    protected function __construct()
    {

    }
    public static function ultimaVenta($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select max(ven_idLocal) as ven_id from venta where idSucursal=:idSucursal";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            if(isset($postrequest->idSucursal)){
                $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
                $sentencia -> execute();
                $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
                if($sentencia){
                     $arreglo =
                                [
                                    "estado" => 200,
                                    "success" => "maxima id de venta",
                                    "data" => $resultado
                                ];
                            $codigo=200;
                        } else {
                            $arreglo =
                                [
                                    "estado" => "warning",
                                    "mensaje" => "",
                                    "data" => $resultado
                                ];
                            $codigo=202;
                          }//else
            }else{
                $arreglo =
                    [
                        "estado" => 400,
                        "error" => "id de Sucursal necesaria",
                        "data" => $postrequest
                    ];
                $codigo=400;
            }
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =LogIn::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function Parametro($request,$response){
        $postrequest = json_decode($request->getBody());

        $comando = "SELECT idSucursal, accion, parametro, valor, comentario, usuario, fechaActualizacion FROM ms_parametro WHERE idSucursal=:idSucursal";

        try {
            $idSucursal=$postrequest->idSucursal;
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$idSucursal );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
               $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 202,
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Parametros",
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }
    public static function actualizarInventario($request,$response){
        $postrequest = json_decode($request->getBody());
        $db = getConnection();
        $comando2 = "UPDATE ms_inventario SET idEstado='E' WHERE idEstado='A' AND idSucursal=:idSucursal;";
        try{
            $sentencia = $db->prepare($comando2);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $resultado = $sentencia -> execute();
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
                                "mensaje" => "no se actualizo inventario",
                                "data" => $resultado
                            ];
                        return $response->withJson($arreglo, 202, JSON_UNESCAPED_UNICODE);
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
    public static function exportarInventarioAPI($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT a.cat_id,ms.idInventario, ms.idSucursal, ms.art_id, ms.existenciaSolicitud, ms.existenciaRespuesta, ms.idUsuario, ms.fechaSolicitud, ms.fechaRespuesta, ms.existenciaEjecucion, ms.idEstado FROM ms_inventario ms
                          inner join articulo a on(a.art_id = ms.art_id and ms.idSucursal=a.idSucursal)
                          WHERE ms.idEstado='A'
                          AND ms.idSucursal=:idSucursal;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if($resultado){
                    $arreglo = [
                        "estado" => 200,
                        "success"=>"OK",
                        "data" => $resultado
                    ];
                    $codigo=200;
            }
            else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al exportar Inventario",
                    "data" => $resultado
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function usuario($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select idUsuario,usuario,password,nombre,apellido,sexo,contacto,idNivelAutorizacion,idEstado,fechaEstado,fechaSesion,idSucursal from ms_usuario
                  where idSucursal=:idSucursal";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if($resultado){
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "",
                        "data" => $resultado
                    ];
                $codigo=200;
            } else {
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "",
                        "data" => $resultado
                    ];
                $codigo=202;
            }
        }catch(PDOException $e){
            $codigoDeError=$e->getCode();
            $error =LogIn::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }finally{
            $db=null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function generarDashBoard($request,$response){
        //$postrequest = json_decode($request->getBody());
        $query = "select ms.idSucursal,ms.nombre as nombreSucursal,
                    sum(case when mi.idEstado='P' then 1 else 0 end) as inventarioActual,
                    sum(case when mi.idEstado='E' then 1 else 0 end) as inventarioTotal,
                    (sum(case when mi.idEstado='P' then 1 else 0 end)/sum(case when mi.idEstado='E' then 1 else 0 end))*100 as porcentaje
                    from ms_inventario mi
                    inner join ms_sucursal ms on (ms.idSucursal=mi.idSucursal)
                    group by ms.idSucursal";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            //sentencia->bindParam("",$postrequest->algunargumento);
            $ejecucion=$sentencia -> execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if($ejecucion){
                 $arreglo =
                            [
                                "estado" => 200,
                                "success" => "ok",
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
            $error =self::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => ""
            ];
            return $response->withJson($arreglo,400);
        }
    }
    public static function actualizarDashBoard($request,$response){
        $postrequest = json_decode($request->getBody());
        //var_dump($postrequest);
        $query = "UPDATE ms_parametro SET valor=:valor,comentario=:comentario,fechaActualizacion=now()
                  where idSucursal=:idSucursal and accion=:accion and parametro=:parametro";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("valor", $postrequest->valor);
            $sentencia->bindParam("comentario", $postrequest->comentario);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("accion", $postrequest->accion);
            $sentencia->bindParam("parametro", $postrequest->parametro);
            $resultado = $sentencia -> execute();
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
            return $response->withJson($arreglo,400,JSON_UNESCAPED_UNICODE);
        }
    }
}