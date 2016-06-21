<?php

class detalles_venta
{
    protected function __construct(){
    }

    public static function seleccionar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT (mp.metodo), SUM(v.total) as 'Total', mp.mpa_id
                    FROM venta v
                    INNER JOIN detallev dv ON (v.ven_id=dv.ven_idLocal)
                    INNER JOIN ventatipopago vtp ON (v.ven_id=vtp.ven_id)
                    INNER JOIN metodopago mp ON (mp.mpa_id=vtp.tpa_id)
                    WHERE
                    v.fecha>= :fechaInicio AND v.fecha<= :fechaFin AND v.idSucursal= :idSucursal
                    AND v.idSucursal=dv.idSucursal
                    GROUP BY mp.metodo
                    ";
       // var_dump($postrequest);
        $fechaI = $postrequest->fechaInicio;
        $fechaF = $postrequest->fechaFin;
        $fechaI = $fechaI.' 00:00:00';
        $fechaF = $fechaF.' 23:59:59';
        try {
            $db = getConnection();
            //$db->query("SET NAMES 'utf8'");
            //$db->query("SET CHARACTER SET utf8");
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
                    "estado" => 400,
                    "error" => "Error al traer las estadisticas",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 400);
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