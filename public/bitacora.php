<?php

class bitacora
{
    protected function __construct(){
    }

    public static function seleccionar ($request,$response){
         $postrequest = json_decode($request->getBody());

         $fechaI = $postrequest->hora_inicio;
         $fechaF = $postrequest->hora_fin;
         $fechaI = $fechaI.' 00:00:00';
         $fechaF = $fechaF.' 23:59:59';
        $comando = "SELECT * from ms_bitacora WHERE :fechaIni<=fecha AND fecha<=:fechaFin";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
                $sentencia->bindParam("fechaIni",$fechaI);
                $sentencia->bindParam("fechaFin",$fechaF);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado){
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            }else{
                $arreglo = [
                    "estado" => "warning",
                    "success"=>"No se encontraron registros en el rango solicitado",
                    "data" => $resultado
                ];;
                return $response->withJson($arreglo,200);
            }
        }catch(PDOException $e){
            $arreglo = [
                "estado" => 400,
                "error" => general::traducirMensaje($e->getCode(),$e),
                "data" => $e->getMessage()
            ];
            return $response->withJson($arreglo,400);
        }
    }
}