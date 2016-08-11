<?php

/**
 * Created by PhpStorm.
 * User: Ryu
 * Date: 2016-07-20
 * Time: 16:19
 */
class ajuste
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     * Función que selecciona el cabecero de la opción Proceso- Ajustar Inventario
     */
    public static function seleccionar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        $db=null;
        $comando =
        "SELECT msi.idSucursal                                      AS idSucursal,          /*id de la sucursal*/
                       mss.nombre                                   AS descripcionSucursal, /*nombre de la sucursal*/
                       date(msi.fechaSolicitud)                     AS fechaSolicitud,      /*fecha de la solicitud, no confundir con la fecha de respuesta*/
                       count(1)                                     AS cantidadTotalInventario,/*cantidad en variedad de productos*/
                       sum(CASE
                               WHEN msi.idEstado='P' THEN '1'
                               ELSE 0
                           END)                                     AS cantidadInventario,/*cantidad en variedad de productos inventariados*/
                       /*sum(CASE
                               WHEN existenciaSolicitud<=0 THEN 0
                               ELSE existenciaSolicitud
                           END)                                     AS cantidadTotal ,*//*suma total en cantidad de productos por inventariar*/
                       /*sum(CASE
                               WHEN existenciaEjecucion<=0 THEN 0
                               ELSE existenciaEjecucion
                           END)                                     AS cantidadInventariada,*//*suma total en cantidad de productos inventariados*/
                       /*sum(existenciaRespuesta-existenciaEjecucion) AS diferencia,*//*diferencia entre lo inventariado y lo real*/
                       round(sum(CASE
                                     WHEN existenciaRespuesta!=existenciaEjecucion
                                      THEN
                                      (precioCompra/factor)*(existenciaRespuesta-existenciaEjecucion)
                                     ELSE 0
                                 END),2)                            AS costo  /*costo en base al precio de compra*/
                FROM ms_inventario msi
                INNER JOIN ms_sucursal mss ON (mss.idSucursal =msi.idSucursal
                                               AND mss.idSucursal=:idSucursal)
                INNER JOIN articulo a ON (a.art_id = msi.art_id)
                WHERE date(msi.fechaSolicitud)>=date(:fechaIni)
                  AND date (msi.fechaRespuesta)<=date(:fechaFin)
                GROUP BY msi.idSucursal,date(fechaSolicitud)";
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

    /***
     * @param $request
     * @param $response
     * @return mixed
     * Función que devuelve el detalle del cabecero seleccionado de la opción Proceso - Ajustar Inventario
     */
    public static function detalle($request,$response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo=200;
        $db=null;
        $comando =
            "SELECT
                   msi.fechaSolicitud,
                   msi.idInventario,
                   a.clave,
                   a.descripcion,
                   msi.existenciaEjecucion,
                   msi.existenciaRespuesta,
                   sum(case when msi2.idInventario is null then 0 else 1 end) cantidadDeVueltas,
                   round((msi.existenciaRespuesta-msi.existenciaEjecucion),2) as diferencia,
                    round(sum(CASE
                         WHEN msi.existenciaRespuesta!=msi.existenciaEjecucion
                          THEN
                          (a.precioCompra/a.factor)*(msi.existenciaRespuesta-msi.existenciaEjecucion)
                         ELSE 0
                        END),2) as costoActual,
                    round(sum(
                      case when msa.idAjuste is not NULL THEN
                         (msa.precioCompra/msa.factor)*(msa.cantidadAnterior-msa.cantidadAjuste)
                         else 0
                         end
                    ),2) as costoAjuste,
                      case when msi3.idInventario is not NULL and msa.idAjuste is null THEN
                          concat('REGISTRO NUEVO EL DIA :',max(date(msi3.fechaSolicitud)))
                          when msa.idAjuste is not null THEN
                          'REGISTRO YA AJUSTADO'
                          ELSE
                          'EDICION'
                          END
                          as edicion,
                          msi.idInventario as aplicar,
                          case when msi3.idInventario is not NULL THEN/*no aplicar*/
                          0
                          when msa.idAjuste is not null THEN/*no aplicar*/
                          0
                          ELSE
                          1/*aplicar*/
                          END
                          as aplicarCheckBox
                    from ms_inventario msi
                    inner join articulo a on (a.art_id = msi.art_id
                                              and a.idSucursal=msi.idSucursal)
                    left join ms_inventario msi2 on (msi2.art_id = msi.art_id
                                                     and msi2.idSucursal=msi.idSucursal
                                                     and msi.fechaSolicitud>msi2.fechaSolicitud)
                    left join ms_inventario msi3 on (msi3.art_id = msi.art_id
                                                     and msi3.idSucursal=msi.idSucursal
                                                     and msi.fechaSolicitud<msi3.fechaSolicitud)
                    left join ms_ajuste msa on (msa.idInventario = msi.idInventario)
                      where msi.idEstado = 'P'
                            and msi.idSucursal=:idSucursal
                            and date(msi.fechaSolicitud)=date(:fecha)
					group by msi.art_id

                ";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $fecha=trim($postrequest->fecha);
            $sentencia->bindParam("idSucursal",$postrequest->idSucursal);
            $sentencia->bindParam("fecha",$fecha);
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


    public static function Insertar($request,$response){
        $postrequest = json_decode($request->getBody());
        $db=null;
        $contador=0;
        $comandoUpdate="insert into ms_ajuste(idInventario, fecha, idUsuario, clave, precioCompra, factor, cantidadAnterior, cantidadAjuste, idEstado)
                                    values(
                                    :idInventario,
                                    now(),
                                    :idUsuario,
                                    (select clave from articulo where art_id = (select art_id from ms_inventario where idInventario=:idInventario)),
                                    (select precioCompra from articulo where art_id = (select art_id from ms_inventario where idInventario=:idInventario)),
                                    (select factor from articulo where art_id = (select art_id from ms_inventario where idInventario=:idInventario)),
                                    (select existenciaEjecucion from ms_inventario where idInventario=:idInventario),
                                    (select existenciaRespuesta from ms_inventario where idInventario=:idInventario),
                                    'A'
                                    )";
        try {
            $db = getConnection();
            $db->beginTransaction();
            foreach ($postrequest as $renglon ) {
                $sentencia = $db->prepare($comandoUpdate);
                $sentencia->bindParam("idInventario", $renglon->idInventario);
                $sentencia->bindParam("idSucursal", $renglon->idSucursal);
                $sentencia->bindParam("idUsuario", $renglon->idUsuario);
                $sentencia->execute();
                $contador++;
            }
            $arreglo = [
                "estado" => 200,
                "success" => "Se a importado ".$contador." registros",
                "datos" => $contador
            ];
            $db->commit();
            return $response->withJson($arreglo, 200);
        } catch (PDOException $e) {
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