<?php

class detalles_venta
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT *
                            FROM
                              (SELECT (mp.nombre) AS metodo,
                                                     SUM(vtp.total-v.cambio) AS 'Total',
                                                                                mp.tpa_id AS mpa_id
                               FROM venta v
                               INNER JOIN ventatipopago vtp ON (v.ven_id=vtp.ven_id)
                               INNER JOIN tipopago mp ON (mp.tpa_id=vtp.tpa_id)
                               WHERE v.fecha>=:fechaInicio
                                 AND v.fecha<=:fechaFin
                                 AND v.idSucursal like :idSucursal
                                 AND v.status='1'
                               GROUP BY mp.nombre
                               UNION ALL SELECT 'TOTAL' AS metodo,
                                                           sum(vtp.total-v.cambio) AS 'Total',
                                                                                      '%' AS mpa_id
                               FROM venta v
                               INNER JOIN ventatipopago vtp ON (v.ven_id = vtp.ven_id)
                               WHERE v.fecha>=:fechaInicio
                                 AND v.fecha<=:fechaFin
                                 AND v.idSucursal like :idSucursal
                                 AND v.status='1') tt
                            ORDER BY tt.mpa_id desc ;
                    ";
       // var_dump($postrequest);
        $fechaI = $postrequest->fechaInicio;
        $fechaF = $postrequest->fechaFin;
        $fechaI = $fechaI.' 00:00:00';
        $fechaF = $fechaF.' 23:59:59';
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$postrequest->idSucursal);
            $sentencia->bindParam('fechaInicio',$fechaI);
            $sentencia->bindParam('fechaFin',$fechaF);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200,JSON_UNESCAPED_UNICODE);
            } else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "No existen registros para los parametros especificados",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        } finally {
            $db = null;
        }
    }
    public static function seleccionarDetalles($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        //var_dump($postrequest);
        $comando = "select v.tic_id, dv.descripcion, v.fecha, v.total from venta v
                    inner join detallev dv on (v.ven_id=dv.ven_id)
                    inner join ventatipopago vtp on (v.ven_id=vtp.ven_id)
                    where vtp.tpa_id like :idMetodo
                    and v.fecha>=:fechaInicio
                    and v.fecha<=:fechaFin
                    and dv.idSucursal like :idSucursal
                    and vtp.idSucursal like :idSucursal;";

        $fechaI = $postrequest->fechaInicio;
        $fechaF = $postrequest->fechaFin;
        $fechaI = $fechaI.' 00:00:00';
        $fechaF = $fechaF.' 23:59:59';
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal',$postrequest->idSucursal);
            $sentencia->bindParam('idMetodo',$postrequest->idMetodo);
            $sentencia->bindParam('fechaInicio',$fechaI);
            $sentencia->bindParam('fechaFin',$fechaF);

            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($resultado);

            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success" => "OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200,JSON_UNESCAPED_UNICODE);
            } else {
                $arreglo = [
                    "estado" => "warning",
                    "error" => "No existe registro con los parametros especificados",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        } finally {
            $db = null;
        }
    }
}