<?php

class tipo_pago
{
    protected function __construct(){
    }

    public static function insertar($request, $response)
    {
        $postrequest = json_decode($request->getBody());
        $db=null;
        try {
            $db = getConnection();
            $db->beginTransaction();
            $banderaEjecucion=false;

            foreach ($postrequest as $renglon ) {
                $ven_id=$renglon->ven_id;
                $tpa_id = $renglon->tpa_id;
                $total = $renglon->total;
                $monTotal = $renglon->monTotal;

                $comando="INSERT INTO ventatipopago (ven_id, tpa_id, total, monTotal) VALUES (:ven_id,:tpa_id,:total,:monTotal) on
                            duplicate key update ven_id=:ven_id,tpa_id=:tpa_id,total=:total,monTotal=:monTotal;";

                $sentencia = $db->prepare($comando);

                $sentencia->bindParam('ven_id', $ven_id);
                $sentencia->bindParam('tpa_id', $tpa_id);
                $sentencia->bindParam('total', $total);
                $sentencia->bindParam('monTotal', $monTotal);

                $banderaEjecucion=$sentencia->execute();
                if($banderaEjecucion==false){
                    break;
                }
            }
            if($banderaEjecucion){
                $arreglo = [
                    "estado" => 200,
                    "success" => "Se a importado la informacion",
                    "datos" => $sentencia
                ];
                $db->commit();
                return $response->withJson($arreglo, 200);
            }else{
                $db->rollBack();
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al Insertar o Actualizar un registro",
                    "datos" => ""
                ];
                return $response->withJson($arreglo, 400);
            }
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