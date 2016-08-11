<?php
class inventario
{
    protected function __construct(){
    }

    public static function seleccionarAzar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo = null;
        $codigo = 200;
        foreach($postrequest->articulos as $category){
            $new_arr[] = $category;
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "SELECT art_id,
                               clave,
                               descripcion,
                               existencia
                        FROM articulo a
                        INNER JOIN categoria c ON (a.cat_id=c.cat_id
                                                   AND c.idSucursal=:idSucursal)
                        INNER JOIN departamento d ON (d.dep_id=c.dep_id
                                                      AND d.idSucursal=:idSucursal)
                        WHERE a.servicio!=1
                          AND a.cat_id LIKE :cat_id
                          AND d.dep_id LIKE :dep_id
                          AND a.idSucursal LIKE :idSucursal
                          AND art_id NOT IN (".$res_arr.")
                          AND art_id NOT IN
                            (SELECT art_id
                             FROM ms_inventario
                             WHERE fechaSolicitud>curdate()-7
                               AND idEstado NOT IN ('E',
                                                    'A')
                             union all
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                          AND a.art_id NOT IN
                            ( SELECT msp33.valor
                             FROM ms_parametro msp11
                             INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion
                                                               AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                             INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion
                                                               AND msp33.parametro=msp11.valor)
                             WHERE msp11.accion = 'CONFIG_GENERAR_INVENTARIO'
                               AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                               AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                               AND msp11.comentario='TRUE'
                               AND msp33.parametro=:idSucursal)
                          AND a.idSucursal=:idSucursal
                        ORDER BY rand() LIMIT :sta1;";

        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindParam("dep_id", $postrequest->dep_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Azar",
                    "data" => $resultado
                ];;
                $codigo =202;
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
            return $response->withJson($arreglo,$codigo);
        }
    }

    public static function seleccionarIndividual($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        foreach($postrequest->articulos as $category)
        {
            $new_arr[] = $category;
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "SELECT art_id,
                               clave,
                               descripcion,
                               existencia
                        FROM articulo a
                        WHERE a.servicio!=1

                          AND (a.clave=:input
                               OR descripcion LIKE CONCAT('%',:input,'%'))
                          AND a.art_id NOT IN
                            (
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                          AND art_id NOT IN ($res_arr)
                          AND a.art_id NOT IN
                            ( SELECT msp33.valor
                             FROM ms_parametro msp11
                             INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion
                                                               AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                             INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion
                                                               AND msp33.parametro=msp11.valor)
                             INNER JOIN articulo a ON (a.art_id = msp33.valor)
                             WHERE msp11.accion = 'CONFIG_GENERAR_INVENTARIO'
                               AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                               AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                               AND msp11.comentario='TRUE')
                          AND a.idSucursal=:idSucursal";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);

            $sentencia->bindParam("input", $postrequest->input);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Individual",
                    "data" => $postrequest
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

    public static function seleccionarMasVendidos($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo = null;
        $codigo = 200;
        if(isset($postrequest->articulos)) {
            foreach ($postrequest->articulos as $category) {
                $new_arr[] = $category;
            }
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "select a.art_id,a.clave,a.descripcion,a.existencia from venta v
                                inner join detallev dv on (dv.ven_id = v.ven_id)
                                inner join articulo a on (a.art_id = dv.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)
                      INNER JOIN departamento d on (d.dep_id=c.dep_id)
                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                where
                                v.fecha>=:fechaInicio
                                and v.fecha<=:fechaFin
                                and c.cat_id like :cat_id

                                and a.art_id not in ($res_arr)
                                and a.art_id not in
                                (SELECT art_id
                             FROM ms_inventario
                             WHERE fechaSolicitud>curdate()-7
                               AND idEstado NOT IN ('E',
                                                    'A')
                             union all
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                                 and a.art_id NOT IN (
                            SELECT msp33.valor FROM ms_parametro msp11

                        INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                        INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion AND msp33.parametro=msp11.valor)
                        INNER JOIN articulo a ON (a.art_id = msp33.valor)
                        WHERE
                        msp11.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                        AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE'
                        and msp11.comentario='TRUE')
                        and d.dep_id like :dep_id
                                group by a.art_id
                                order by count(*) desc
                            limit :sta1;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("fechaInicio", $postrequest->fechaInicio);
            $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindParam("dep_id",$postrequest->dep_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Vendido",
                    "data" => $resultado
                ];;
                $codigo =202;
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "datos" => $e->getMessage()
            ];
            $codigo = 400;
        }
        finally{
            $db=null;
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function insertar($request, $response)
    {
        set_time_limit(0);
        $postrequest = json_decode($request->getBody());
        $db=null;
        $resultado=null;
        $codigo = 200;

        try {
            $db = getConnection();
            $db->beginTransaction();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            foreach ($postrequest->art_id as $renglon ) {
                $art_id = $renglon;

                $comando = "INSERT INTO ms_inventario(idInventario, idInventarioLocal, idSucursal, art_id,
                existenciaSolicitud, existenciaRespuesta, idUsuario, fechaSolicitud, existenciaEjecucion, idEstado)
                            VALUES (0,
                                    0,
                                    :idSucursal,
                                    :art_id,
                                    0,
                                    0,
                                    :idUsuario,
                                    NOW(),
                                    0,
                                    'A')";

                $sentencia = $db->prepare($comando);
                $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
                $sentencia->bindParam("art_id", $art_id);
                $sentencia->bindParam("idUsuario", $postrequest->idUsuario);
                $resultado = $sentencia->execute();
            }
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                $db->commit();

            } else {
                $db->rollBack();
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al insertar Registros, asegurese que la lista no este vacia",
                    "datos" => $resultado
                ];
                $codigo=400;
            }
        } catch (PDOException $e) {
            $db->rollBack();
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

    public static function seleccionarMasConflictivos($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        if(isset($postrequest->articulos)) {
            foreach ($postrequest->articulos as $category) {
                $new_arr[] = $category;
            }
        }
        $res_arr=!isset($new_arr)?'0':implode(',',$new_arr);
            $comando = "select a.art_id,a.clave,a.descripcion,a.existencia,count(*) as repetido from
                                ms_inventario msi
                                inner join articulo a on (a.art_id = msi.art_id)
                                inner join categoria c on (c.cat_id = a.cat_id)
                                inner join ms_sucursal ms on (ms.idSucursal =:idSucursal)
                                INNER JOIN departamento d on (d.dep_id=c.dep_id)
                                where
                                msi.fechaSolicitud>=:fechaInicio
                                and msi.fechaSolicitud<=:fechaFin

                                and msi.existenciaRespuesta!=msi.existenciaEjecucion
                                and c.cat_id like :cat_id
                                and a.art_id not in ($res_arr)
                                and msi.art_id not in
                                (SELECT art_id
                             FROM ms_inventario
                             WHERE fechaSolicitud>curdate()-7
                               AND idEstado NOT IN ('E',
                                                    'A')
                             union all
                             select art_id
                             from ms_inventario
                             where idEstado in ('E','A')
                            )
                                 and a.art_id NOT IN (
                            SELECT msp33.valor FROM ms_parametro msp11

                        INNER JOIN ms_parametro msp22 ON (msp22.accion=msp11.accion AND msp22.parametro='_COMPONENTE_LISTADO_EXCLUYENTE')
                        INNER JOIN ms_parametro msp33 ON (msp22.valor=msp33.accion AND msp33.parametro=msp11.valor)
                        INNER JOIN articulo a ON (a.art_id = msp33.valor)
                        WHERE
                        msp11.accion = 'CONFIG_GENERAR_INVENTARIO' AND msp11.parametro='BANDERA_LISTA_EXCLUYENTE_SUC'
                        AND msp33.accion='LISTA_RELACION_IDSUCURSAL_ARTID_EXCLUYENTE')
                        and d.dep_id like :dep_id
                                group by a.art_id
                               having count(*)>=3
                                order by count(*) desc
                            limit :sta1;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("fechaInicio", $postrequest->fechaInicio);
            $sentencia->bindParam("fechaFin", $postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("cat_id", $postrequest->cat_id);
            $sentencia->bindParam("dep_id",$postrequest->dep_id);
            $sentencia->bindValue(':sta1', (int) $postrequest->cantidad, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                $codigo=200;
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Conflictivos",
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
            return $response->withJson($arreglo, $codigo);
        }
    }
    public static function reporteCabecero($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
            $comando = "select msi.idSucursal, mss.nombre, date(msi.fechaSolicitud) as fecha, count(1) total,
							sum(case when existenciaRespuesta=existenciaEjecucion and existenciaRespuesta!=0
                            then '1' else 0 end) as totalAcertado,sum(case when existenciaRespuesta!=existenciaEjecucion
                            then '1' else 0 end) as totalFallado,
                            round(sum(case when existenciaRespuesta!=existenciaEjecucion then
                                    (precioCompra/factor)*(existenciaRespuesta-existenciaEjecucion) else 0 end),2) as costo,
							round((sum(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
            else existenciaEjecucion/existenciaRespuesta end)/count(*)),2) as bandera
            	             from
                            ms_inventario msi
                            inner join ms_sucursal mss on (mss.idSucursal = msi.idSucursal and mss.idSucursal=:idSucursal)
                            INNER JOIN articulo a ON (a.art_id = msi.art_id
                                              AND a.idSucursal = msi.idSucursal)
                            where date(msi.fechaSolicitud)>=date(:fechaIni)
                            and date(msi.fechaRespuesta)<=date(:fechaFin)
                            group by msi.idsucursal, fecha
                            order by fecha,a.art_id";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $fechaIni=trim($postrequest->fechaInicio);
            $fechaFin=trim($postrequest->fechaFin);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("fechaIni",$fechaIni);
            $sentencia->bindParam("fechaFin",$fechaFin);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];

            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Conflictivos",
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
            return $response->withJson($arreglo, $codigo);
        }
    }

    public static function reporteDetalle($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        /*$comando = "select msi.fechaSolicitud, a.clave,a.descripcion, msi.existenciaSolicitud,msi.existenciaEjecucion,
            msi.existenciaRespuesta,(msi.existenciaRespuesta-msi.existenciaEjecucion) as diferencia,(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
            else existenciaEjecucion/existenciaRespuesta end) as bandera from
            ms_inventario msi inner join articulo a on (a.art_id = msi.art_id and
            a.idSucursal = msi.idSucursal)
                    where msi.idSucursal =:idSucursal
                    and date(msi.fechaSolicitud)=date(:fecha);";*/
        $comando = "SELECT msi.fechaRespuesta as fechaSolicitud,
                           a.clave,a.descripcion,
                           msi.existenciaSolicitud,
                           msi.existenciaEjecucion,
                           msi.existenciaRespuesta,
                           round((msi.existenciaRespuesta-msi.existenciaEjecucion),2) AS diferencia,
                           round((CASE
                              WHEN existenciaEjecucion>=existenciaRespuesta THEN
                              existenciaRespuesta/existenciaEjecucion ELSE
                              existenciaEjecucion/existenciaRespuesta
                            END),2) AS bandera,
                           round((precioCompra/factor)*(existenciaRespuesta-existenciaEjecucion),2) as costo
                    FROM ms_inventario msi
                    INNER JOIN articulo a ON (a.art_id = msi.art_id
                                              AND a.idSucursal = msi.idSucursal)
                    WHERE msi.idSucursal =:idSucursal
                      AND date(msi.fechaSolicitud)=:fecha
                    GROUP BY msi.idSucursal,
                             a.art_id;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("fecha", $postrequest->fecha);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al traer listado de Inventario Mas Conflictivos",
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
            return $response->withJson($arreglo, $codigo);
        }
    }




}