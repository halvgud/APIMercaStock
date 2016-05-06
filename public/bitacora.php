<?php
class bitacora
{
    protected function __construct()
    {

    }

    public static function seleccionarBitacora ($request,$response,$args){
        /* $postrequest = json_decode($request->getBody());
         //return $response->withJson(var_dump($postrequest),400);;
         $fechaI = $postrequest->hora_inicio;
         $fechaF = $postrequest->hora_fin;
         $fechaI = $fechaI.' 00:00:00';
         $fechaF = $fechaF.' 23:59:59';*/
        $comando = "SELECT * from ms_bitacora";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            //    $sentencia->bindParam("fechaIni",$fechaI);
            //    $sentencia->bindParam("fechaFin",$fechaF);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado){
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado[0]
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
                "error"=>"Error al traer la Bitácora",
                "data" => $e
            ];
            return $response->withJson($arreglo,400);//json_encode($wine);
        }

    }
}