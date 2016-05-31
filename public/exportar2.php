<?php
class exportar2{
    protected function _construct(){

    }

    public static function exportarInventarioAPI($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $comando = "SELECT idInventario, idInventarioLocal, idSucursal, art_id, existenciaSolicitud, existenciaRespuesta, idUsuario, fechaSolicitud, fechaRespuesta, existenciaEjecucion, idEstado FROM ms_inventario
                          WHERE idEstado='A'
                          AND idSucursal=:idSucursal;";
        try {
            $db = getConnection();
            $db->query("SET NAMES 'utf8'");
            $db->query("SET CHARACTER SET utf8");
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam("idSucursal", $postrequest->idSucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($resultado);
            if($resultado){
                $comando2 = "UPDATE ms_inventario SET idEstado='E' WHERE idEstado='A' AND idSucursal=:idSucursal;";

                $sentencia2 = $db->prepare($comando2);
                $sentencia2->bindParam("idSucursal", $postrequest->idSucursal);
                $sentencia2->execute();
                $resultado2 = $sentencia2->rowCount();
                if ($resultado2>0) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
                    } else {
                        $arreglo = [
                            "estado" => 'warning',
                            "success" => "Error al cambiar Estado",
                            "data" => $resultado2
                        ];
                        return $response->withJson($arreglo, 200);
                    }
            }
            else {
                $arreglo = [
                    "estado" => 'warning',
                    "success" => "Error al exportar Inventario",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo, 200);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Inventario",
                "datos" => $e->getMessage()
            ];
            return $response->withJson($arreglo, 400);
        }
        finally{
            $db=null;
        }
    }
}