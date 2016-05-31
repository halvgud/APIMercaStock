<?php
class importar2{
    protected function _construct(){

    }

    public static function importarInventarioAPI($request, $response){
        $postrequest = json_decode($request->getBody());


        try {
            $db = getConnection();
            $query = "UPDATE ms_inventario SET idInventarioLocal=:idInventarioLocal, existenciaSolicitud=:existenciaSolicitud, existenciaRespuesta=:existenciaRespuesta, idUsuario=:idUsuario, fechaSolicitud=:fechaSolicitud, fechaRespuesta=:fechaRespuesta, existenciaEjecucion=:existenciaEjecucion, idEstado=:idEstado
                    WHERE idInventario=:idInventario AND art_id=:art_id AND ms_inventario.idSucursal=:idSucursal ;";
            $sentencia = $db->prepare($query);

            foreach ($postrequest->data as $renglon ) {

            $sentencia->bindParam("idInventario", $renglon->idInventario, PDO::PARAM_STR);
            $sentencia->bindParam("art_id", $renglon->art_id, PDO::PARAM_STR);
            $sentencia->bindParam("idSucursal", $renglon->idSucursal, PDO::PARAM_STR);
            $sentencia->bindParam("idInventarioLocal", $renglon->idInventarioLocal, PDO::PARAM_STR);
            $sentencia->bindParam("existenciaSolicitud", $renglon->existenciaSolicitud, PDO::PARAM_STR);
            $sentencia->bindParam("existenciaRespuesta", $renglon->existenciaRespuesta, PDO::PARAM_STR);
            $sentencia->bindParam("idUsuario", $renglon->idUsuario, PDO::PARAM_STR);
            $sentencia->bindParam("fechaSolicitud", $renglon->fechaSolicitud, PDO::PARAM_STR);
            $sentencia->bindParam("fechaRespuesta", $renglon->fechaRespuesta, PDO::PARAM_STR);
            $sentencia->bindParam("existenciaEjecucion", $renglon->existenciaEjecucion, PDO::PARAM_STR);
            $sentencia->bindParam("idEstado",$renglon->idEstado, PDO::PARAM_STR);
            $resultado = $sentencia->execute();
        }
            if ($resultado) {
                $arreglo =
                    [
                        "estado" => 200,
                        "success" => "Se a actualizado con éxito",
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
            $error = 'Error';
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
}