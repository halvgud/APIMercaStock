<?php
class exportar
{
    protected function __construct()
    {

    }
    public static function ultimaVenta($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select (case when max(ven_id) is null then 0 else max(ven_id) end) as ven_id from venta where idSucursal=:idSucursal";
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

            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function ultimoDetalleVenta($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select (case when max(ven_id) is null then 0 else max(ven_id) end) as ven_id from detallev where idSucursal=:idSucursal";
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
                            "success" => "maxima id de detalle venta",
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

            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo,$codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function ultimoVentaTipoPago($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select (case when max(ven_id) is null then 0 else max(ven_id) end) as ven_id from ventatipopago where idSucursal=:idSucursal";
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
                            "success" => "maxima id de tipo pago",
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

            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
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

        $comando = "SELECT idSucursal, accion, parametro, valor, comentario, usuario, fechaActualizacion FROM ms_parametro WHERE idSucursal=:idSucursal
                    and accion!='CONFIG_DASHBOARD'";

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
                "error" => general::traducirMensaje($e->getCode(),$e),
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
        $db=null;

        $comando2 = "UPDATE ms_inventario SET idEstado='E' WHERE idEstado='A' AND idSucursal=:idSucursal;";
        try{
            $db = getConnection();
            $db->beginTransaction();
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
                $db->commit();
                return $response->withJson($arreglo, 200, JSON_UNESCAPED_UNICODE);
            } else {
                $db->rollBack();
                $arreglo =
                    [
                        "estado" => "warning",
                        "mensaje" => "no se actualizo inventario",
                        "data" => $resultado
                    ];
                return $response->withJson($arreglo, 202, JSON_UNESCAPED_UNICODE);
            }//else
        }catch(PDOException $e){
            $db->rollBack();

            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
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
                          WHERE ms.idEstado in('A','I')
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
                "error" => general::traducirMensaje($e->getCode(),$e),
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
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($postrequest)
            ];
            $codigo=400;
        }finally{
            $db=null;
            return $response->withJson($arreglo, $codigo,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function generarDashBoard($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select ms.idSucursal,ms.nombre as nombreSucursal,
                    sum(case when mi.idEstado='P' then 1 else 0 end) as inventarioActual,
                    sum(case when mi.idEstado in ('E','P') then 1 else 0 end) as inventarioTotal,
                    (sum(case when mi.idEstado='P' then 1 else 0 end)/sum(case when mi.idEstado in ('P','E') then 1 else 0 end))*100 as porcentaje
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
            $error =general::traducirMensaje($codigoDeError,$e);
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
            $error =general::traducirMensaje($codigoDeError,$e);
            $arreglo = [
                "estado" =>$e -> getCode(),
                "error" =>$error,
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo,400,JSON_UNESCAPED_UNICODE);
        }
    }
    public static function GenerarDashBoardPorSucursal($request,$response){
        $postrequest = json_decode($request->getBody());
        $query = "select mp1.accion,lower(mp1.parametro) as descripcion,mp1.valor as tiempoRestante,
        mp1.comentario as tiempoDefinido,date_add(mp1.fechaActualizacion,interval 1 hour) as fechaActualizacion,
        mp2.valor as icono
        from ms_parametro mp1
        inner join ms_parametro mp2 on
        (mp2.idSucursal='1' and mp2.accion ='ICONO_DASHBOARD' and mp2.parametro = mp1.parametro )
        where mp1.accion='CONFIG_DASHBOARD' and mp1.idSucursal=:idSucursal order by mp1.fechaActualizacion";
        try{
            $db=getConnection();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
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

    public static function exportarAjuste($request,$response){
        $postrequest = json_decode($request->getBody());

        $comando = "SELECT  msa.idAjuste,
                            msa.idInventario,
                            msa.fecha,
                            msa.idUsuario,
                            msa.clave,
                            msa.precioCompra,
                            msa.factor,
                            msa.cantidadAnterior,
                            msa.cantidadAjuste,
                            msa.idEstado
                            from ms_ajuste msa
                             inner join ms_inventario msi on (msi.idInventario=msa.idInventario)
                              where msi.idSucursal=:idSucursal
                              and msa.idEstado='A'";

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
                    "error" => "Error al traer listado de ajustes",
                    "data" => $idSucursal
                ];
                $codigo=202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo=400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function ActualizarAjuste($request, $response)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $contador=0;
        try {
            $db = getConnection();
            $db->beginTransaction();
            foreach ($postrequest->data as $renglon) {
                $contador++;
                $idInventario = $renglon->idInventario!=''?$renglon->idInventario:null;
                $comando = "UPDATE ms_ajuste SET idEstado='E' WHERE idEstado='A' AND idInventario=:idInventario;";
                $sentencia = $db->prepare($comando);
                $sentencia->bindParam('idInventario', $idInventario);
                $sentencia->execute();
            }
            $arreglo = [
                "estado" => 200,
                "success" => "Se a importado la informacion: ".$contador,
                "datos" => $contador
            ];
            $db->commit();
            return $response->withJson($arreglo, 200);
        }
        catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
}