<?php

class perdidas
{


    public static function reporteCabecero($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
       /* $comando = "select msi.idSucursal, mss.nombre, date(msi.fechaSolicitud) as fecha, count(1) total,
							sum(1) as 'totalAcertado',sum(case when existenciaRespuesta!=existenciaEjecucion
                            then '1' else 0 end) as totalFallado,
							(sum(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
            else existenciaEjecucion/existenciaRespuesta end)/count(*)) as bandera	from
                            ms_inventario msi
                            inner join ms_sucursal mss on (mss.idSucursal = msi.idSucursal and mss.idSucursal=:idSucursal)
                            where date(msi.fechaSolicitud)>=date(:fechaIni)
                            and date(msi.fechaRespuesta)<=date(:fechaFin)
                            group by msi.idsucursal, fecha
                            order by fecha";*/
        $comando ="SELECT
                          d.nombre nombre,
                          msie.idInventarioExterno,
                        DATE(max(msi.fechaRespuesta)) AS fecha,
                        COUNT(1) AS TotalEsperado,
                        SUM(CASE
                                WHEN
                                    msi.existenciaRespuesta = msi.existenciaEjecucion
                                    AND msi.idEstado NOT IN ('E' , 'A')
                                THEN '1'
                                ELSE 0
                            END) AS TotalAcertado,
                        SUM(CASE
                                WHEN
                                    msi.existenciaRespuesta != msi.existenciaEjecucion
                                    AND msi.idEstado != 'E'
                                THEN '1'
                                ELSE 0
                            END) AS TotalFallado,
                        SUM(CASE
                                WHEN msi.idEstado IN ('E' , 'A')
                                THEN 1
                                ELSE 0
                            END) AS TotalRestante,
                        ROUND(SUM(CASE
                                    WHEN msi2.existenciaRespuesta < msi2.existenciaEjecucion
                                    THEN (precioCompra / factor) * (msi2.existenciaRespuesta - msi2.existenciaEjecucion)
                                    ELSE 0
                                END),
                                2) AS costo2,
                            ROUND((SUM(CASE
                                    WHEN msi2.existenciaEjecucion >= msi2.existenciaRespuesta
                                    THEN msi2.existenciaRespuesta / msi2.existenciaEjecucion
                                    ELSE msi2.existenciaEjecucion / msi2.existenciaRespuesta
                                END) / COUNT(*)),
                                2) AS bandera2,
                        ROUND(SUM(CASE
                                    WHEN msi.existenciaRespuesta < msi.existenciaEjecucion
                                    THEN (precioCompra / factor) * (msi.existenciaRespuesta - msi.existenciaEjecucion)
                                    ELSE 0
                                END),
                                2) AS costo,
                            ROUND((SUM(CASE
                                    WHEN msi.existenciaEjecucion >= msi.existenciaRespuesta
                                    THEN msi.existenciaRespuesta / msi.existenciaEjecucion
                                    ELSE msi.existenciaEjecucion / msi.existenciaRespuesta
                                END) / COUNT(*)),
                                2) AS bandera,
                                     1 AS detalle
                    FROM
                        ms_inventario msi
                        left join ms_inventario msi2 on (msi.art_id = msi2.art_id and msi2.fechaRespuesta = (select max(msi3.fechaRespuesta)
                                                                                                             from ms_inventario msi3 WHERE
                                                                                                             msi3.art_id = msi2.art_id and msi.fechaRespuesta>msi3.fechaRespuesta))
                            LEFT JOIN
                        ms_inventario_etiqueta msie ON (msie.idInventarioExterno = msi.idInventarioExterno)
                            INNER JOIN
                        ms_sucursal mss ON (mss.idSucursal = msi.idSucursal
                            AND mss.idSucursal =:idSucursal)
                            INNER JOIN
                        articulo a ON (a.art_id = msi.art_id
                            AND a.idSucursal = msi.idSucursal
                            AND a.status = 1)
                            INNER JOIN
                        categoria c ON (c.cat_id = a.cat_id)
                            INNER JOIN
                        departamento d ON (d.dep_id = c.dep_id)
                    WHERE msi.idEstado != 'I'
                    GROUP BY d.dep_id
                    ORDER BY fecha , a.art_id";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
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

    public static function reportePerdidas($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        $comando = "select msi.fechaSolicitud, a.clave,a.descripcion, msi.existenciaSolicitud,msi.existenciaEjecucion,
            msi.existenciaRespuesta,(msi.existenciaEjecucion-msi.existenciaRespuesta) as diferencia,(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
            else existenciaEjecucion/existenciaRespuesta end) as bandera from
            ms_inventario msi inner join articulo a on (a.art_id = msi.art_id and
            a.idSucursal = msi.idSucursal)
                    where msi.idSucursal =:idSucursal
                    and date(msi.fechaSolicitud)=date(:fecha);";
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