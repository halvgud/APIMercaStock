<?php

class parametros
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
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
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
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

    public static function seleccionarListaFijaExcluyente($request,$response){
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT art_id, clave, descripcion, existencia,msp1.comentario FROM ms_parametro msp1

                        INNER JOIN ms_parametro msp2 ON (msp2.accion=msp1.accion AND msp2.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                        INNER JOIN ms_parametro msp3 ON (msp2.valor=msp3.accion AND msp3.parametro=msp1.valor)
                        INNER JOIN articulo a ON (a.art_id = msp3.valor)
                        WHERE
                        msp1.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp1.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                        AND msp3.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                        ";
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
                return $response->withJson($arreglo,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error" => "error al traer el listado de articulos",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
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

    public static function seleccionarListaFija($request,$response){
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT art_id, clave, descripcion, existencia,msp1.comentario FROM ms_parametro msp1

                        INNER JOIN ms_parametro msp2 ON (msp2.accion=msp1.accion AND msp2.parametro='_COMPONENTE_LISTADO')
                        INNER JOIN ms_parametro msp3 ON (msp2.valor=msp3.accion AND msp3.parametro=msp1.valor)
                        INNER JOIN articulo a ON (a.art_id = msp3.valor)
                        WHERE
                        msp1.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp1.parametro='BANDERA_LISTA_FIJA_SUC'
                        AND msp3.accion='LISTA_RELACION_IDSUCURSAL_ARTID'
                        and a.art_id not in (
                        select ms.art_id from ms_inventario ms where ms.idSucursal=:idSucursal
                        and ms.idEstado in ('A')
                        union ALL
                        select ms.art_id from ms_inventario ms where ms.idSucursal=:idSucursal
                        and ms.idEstado in ('E') and ms.fechaSolicitud>CURDATE() - INTERVAL 1 DAY
                        )
                        AND a.idSucursal=:idSucursal";
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
                return $response->withJson($arreglo,200);
            }else{
                $arreglo = [
                    "estado" => 400,
                    "error" => "error al traer el listado de articulos",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
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

    public static function seleccionarListaFijaInventario($request, $response)
    {
        $postrequest = json_decode($request->getBody());
///Buscar en Generar Inventario, agrega artículos que se encuentran el Lista Fija, que no hayan sido agregados al día actual y
        //que no se encuentren en la lista excluyente
        $comando = "select
                    a.art_id,
                    a.clave,
                    a.descripcion,
                    a.existencia,
                    msp1.comentario
                    from articulo a
                    inner join ms_parametro msp1 on (
                        msp1.accion='CONFIG_GENERAR_INVENTARIO' and
                        msp1.parametro='BANDERA_LISTA_FIJA_SUC')
                    inner join ms_parametro msp2 on (msp2.accion = msp1.accion and msp2.parametro='_COMPONENTE_LISTADO')
                    inner join ms_parametro msp3 on (
                        msp3.accion = 'LISTA_RELACION_IDSUCURSAL_ARTID' and
                        msp3.accion =msp2.valor and msp3.parametro = msp1.valor)
                    where a.art_id = msp3.valor
                    and msp3.valor not in (
                        select msi.art_id from
                            ms_inventario msi
                            where msi.fechaSolicitud>curdate()
                            and msi.idEstado = 'A'
                            union all
                            select 0
                            union all
                            select
                    a.art_id
                    from articulo a
                    inner join ms_parametro msp1 on (
                        msp1.accion='CONFIG_GENERAR_INVENTARIO' and
                        msp1.parametro='BANDERA_LISTA_EXCLUYENTE_SUC')
                    inner join ms_parametro msp2 on (msp2.accion = msp1.accion and msp2.parametro='_COMPONENTE_LISTADO')
                    inner join ms_parametro msp3 on (
                        msp3.accion = 'LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE' and
                        msp3.accion =msp2.valor and msp3.parametro = msp1.valor)
                    where a.art_id = msp3.valor
                        )
                AND a.idSucursal =:idSucursal
                and msp1.valor = a.idSucursal
                AND a.existencia > 0
                  ; ";

        try {
            $idSucursal=$postrequest->idSucursal;
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$idSucursal );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if (isset($postrequest->bandera)||(isset($resultado[0])&&$resultado[0]->comentario=="TRUE")) {

                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 'nomessage',
                    "success" => "No necesario",
                    "data" => ""
                ];
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
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

    public static function actualizar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        $query = "UPDATE ms_parametro SET valor=:valor,comentario=:comentario,usuario=:usuario,fechaActualizacion=NOW() WHERE idSucursal=:idSucursal AND accion=:accion AND parametro=:parametro";
        try {
            $db = getConnection();
            $db->beginTransaction();
            $sentencia = $db->prepare($query);
            $sentencia ->bindParam("idSucursal",$postrequest->idSucursal,PDO::PARAM_INT);
            $sentencia->bindParam("accion", $postrequest->accion,PDO::PARAM_STR);
            $sentencia->bindParam("parametro", $postrequest->parametro,PDO::PARAM_STR);
            $sentencia->bindParam("valor", $postrequest->valor,PDO::PARAM_STR);
            $sentencia->bindParam("comentario", $postrequest->comentario,PDO::PARAM_STR);
            $sentencia->bindParam("usuario", $postrequest->usuario,PDO::PARAM_STR);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
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
        } catch (PDOException $e) {
            $db->rollBack();
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

    public static function actualizarListaFija($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        if(isset($postrequest->excluyente)){
            $query = "UPDATE ms_parametro SET comentario=:comentario WHERE valor=:valor AND parametro='BANDERA_LISTA_EXCLUYENTE_SUC'";
        }else{
            $query = "UPDATE ms_parametro SET comentario=:comentario WHERE valor=:valor AND parametro='BANDERA_LISTA_FIJA_SUC'";
        }
        try {
            $db = getConnection();
            $db->beginTransaction();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("valor", $postrequest->valor,PDO::PARAM_STR);
            $sentencia->bindParam("comentario", $postrequest->comentario,PDO::PARAM_STR);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
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
        } catch (PDOException $e) {
            $db->rollBack();
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

    public static function actualizarListaExcluyente($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        $query = "UPDATE ms_parametro SET comentario=:comentario WHERE valor=:valor AND parametro='BANDERA_LISTA_EXCLUYENTE_SUC'";
        try {
            $db = getConnection();
            $db->beginTransaction();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("valor", $postrequest->valor,PDO::PARAM_STR);
            $sentencia->bindParam("comentario", $postrequest->comentario,PDO::PARAM_STR);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado el usuario con éxito",
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
        } catch (PDOException $e) {
            $db->rollBack();
            $codigoDeError = $e->getCode();
            $error = self::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => $error,
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

    public static function insertarListaFija($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        if(isset($postrequest->excluyente)){
            $query = "INSERT INTO ms_parametro VALUES ('1','LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE',:parametro,:valor,'',:usuario,NOW())";
        }else{
            $query = "INSERT INTO ms_parametro VALUES ('1','LISTA_RELACION_IDSUCURSAL_ARTID',:parametro,:valor,'',:usuario,NOW())";
        }

        try {
            $db = getConnection();
            $db->beginTransaction();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("parametro", $postrequest->parametro,PDO::PARAM_STR);
            $sentencia->bindParam("valor", $postrequest->valor,PDO::PARAM_STR);
            $sentencia->bindParam("usuario", $postrequest->usuario,PDO::PARAM_STR);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a agregado con éxito",
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
        } catch (PDOException $e) {
            $db->rollBack();
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

    public static function eliminarListaFija($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        if(isset($postrequest->excluyente)){
            $query = "DELETE FROM  ms_parametro WHERE accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE' AND parametro=:parametro AND valor=:valor";
        }else{
            $query = "DELETE FROM  ms_parametro WHERE accion='LISTA_RELACION_IDSUCURSAL_ARTID' AND parametro=:parametro AND valor=:valor";
        }

        try {
            $db = getConnection();
            $db->beginTransaction();
            $sentencia = $db->prepare($query);
            $sentencia->bindParam("parametro", $postrequest->parametro,PDO::PARAM_STR);
            $sentencia->bindParam("valor", $postrequest->valor,PDO::PARAM_STR);
            $resultado = $sentencia->execute();
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se ha borrado el artículo con éxito",
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
        } catch (PDOException $e) {
            $db->rollBack();
            $codigoDeError = $e->getCode();
            $error = logIn::traducirMensaje($codigoDeError, $e);
            $arreglo = [
                "estado" => $e->getCode(),
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => json_encode($postrequest)
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }

    public static function seleccionarEstado($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        if(isset($postrequest->excluyente)){
            $comando = "SELECT comentario FROM ms_parametro WHERE valor=:idSucursal AND parametro='BANDERA_LISTA_EXCLUYENTE_SUC'";
        }
        else{
            $comando = "SELECT comentario FROM ms_parametro WHERE valor=:idSucursal AND parametro='BANDERA_LISTA_FIJA_SUC'";
        }

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
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Parametros",
                    "data" => $idSucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
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