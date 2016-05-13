<?php
class bitacora
{
    protected function __construct()
    {

    }

    public static function seleccionar ($request,$response,$args){
        $comando = "SELECT * from ms_bitacora";
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
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
                "error"=>"Error al traer la Bitácora",
                "data" => $e
            ];
            return $response->withJson($arreglo,400);
        }

    }
}