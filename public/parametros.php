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
                "error" => "Error al traer listado de Parametros",
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
                "error" => "Error al traer listado de Parametros",
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
                        AND msp3.accion='LISTA_RELACION_IDSUCURSAL_ARTID' ";
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
                "error" => "Error al traer listado de Parametros",
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
        $comando = "SELECT art_id, clave, descripcion, existencia,msp1.comentario FROM ms_parametro msp1
                        INNER JOIN ms_parametro msp2 ON (msp2.accion=msp1.accion AND msp2.parametro='_COMPONENTE_LISTADO')
                        INNER JOIN ms_parametro msp3 ON (msp2.valor=msp3.accion AND msp3.parametro=msp1.valor)
                        INNER JOIN articulo a ON (a.art_id = msp3.valor)
                        WHERE
                        msp1.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp1.parametro='BANDERA_LISTA_FIJA_SUC'
                        AND msp3.accion='LISTA_RELACION_IDSUCURSAL_ARTID' AND
                        msp3.valor NOT IN (SELECT msi.art_id FROM ms_inventario msi WHERE fechaSolicitud>curdate() AND idEstado='A'
                            UNION ALL SELECT 0) AND msp3.valor NOT IN (
                            SELECT msp33.valor FROM ms_parametro msp11

                        INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                        INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion AND msp33.parametro=msp11.valor)
                        INNER JOIN articulo a ON (a.art_id = msp33.valor)
                        WHERE
                        msp11.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                        AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE')
                        and a.existencia>0; ";

        try {
            $idSucursal=$postrequest->idSucursal;
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$idSucursal );
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if (isset($postrequest->bandera)||$resultado[0]->comentario=="TRUE") {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else if($resultado) {
                $arreglo = [
                    "estado" => 'nomessage',
                    "success" => "No necesario",
                    "data" => ""
                ];
                return $response->withJson($arreglo, 200);
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
                "error" => "Error al traer listado de Parametros",
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
        $query = "UPDATE ms_parametro SET valor=:valor,comentario=:comentario,usuario=:usuario,fechaActualizacion=NOW() WHERE idSucursal=:idSucursal AND accion=:accion AND parametro=:parametro";
        try {
            $db = getConnection();
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

    public static function actualizarListaFija($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        if(isset($postrequest->excluyente)){
            $query = "UPDATE ms_parametro SET comentario=:comentario WHERE valor=:valor AND parametro='BANDERA_LISTA_EXCLUYENTE_SUC'";
        }else{
            $query = "UPDATE ms_parametro SET comentario=:comentario WHERE valor=:valor AND parametro='BANDERA_LISTA_FIJA_SUC'";
        }
        try {
            $db = getConnection();
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

    public static function actualizarListaExcluyente($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $query = "UPDATE ms_parametro SET comentario=:comentario WHERE valor=:valor AND parametro='BANDERA_LISTA_EXCLUYENTE_SUC'";
        try {
            $db = getConnection();
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
        if(isset($postrequest->excluyente)){
            $query = "INSERT INTO ms_parametro VALUES ('1','LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE',:parametro,:valor,'',:usuario,NOW())";
        }else{
            $query = "INSERT INTO ms_parametro VALUES ('1','LISTA_RELACION_IDSUCURSAL_ARTID',:parametro,:valor,'',:usuario,NOW())";
        }

        try {
            $db = getConnection();
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

            $arreglo = [
                "estado" => $e->getCode(),
                "error" => 'Error',
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
        if(isset($postrequest->excluyente)){
            $query = "DELETE FROM  ms_parametro WHERE accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE' AND parametro=:parametro AND valor=:valor";
        }else{
            $query = "DELETE FROM  ms_parametro WHERE accion='LISTA_RELACION_IDSUCURSAL_ARTID' AND parametro=:parametro AND valor=:valor";
        }

        try {
            $db = getConnection();
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
                "error" => "Error al traer listado de Parametros",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
}