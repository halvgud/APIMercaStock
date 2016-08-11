<?php

class perdidas
{


    public static function reporteCabecero($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $arreglo=null;
        $codigo = 200;
        $comando = "select msi.idSucursal, mss.nombre, date(msi.fechaSolicitud) as fecha, count(1) total,
							sum(1) as 'totalAcertado',sum(case when existenciaRespuesta!=existenciaEjecucion
                            then '1' else 0 end) as totalFallado,
							(sum(case when existenciaEjecucion>=existenciaRespuesta then existenciaRespuesta/existenciaEjecucion
            else existenciaEjecucion/existenciaRespuesta end)/count(*)) as bandera	from
                            ms_inventario msi
                            inner join ms_sucursal mss on (mss.idSucursal = msi.idSucursal and mss.idSucursal=:idSucursal)
                            where date(msi.fechaSolicitud)>=date(:fechaIni)
                            and date(msi.fechaRespuesta)<=date(:fechaFin)
                            group by msi.idsucursal, fecha
                            order by fecha";
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