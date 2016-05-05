<?php
class categoria
{
    protected function __construct()
    {

    }

    public static function seleccionarCategoria($request, $response, $args)
    {
        $postrequest = json_decode($request->getBody());
        //echo($postrequest);
        // console.log($postrequest);
        if($postrequest->sucursal=='TODOS'){
            $comando = "SELECT * FROM categoria";
        }
        else {
            $comando = "SELECT * FROM categoria WHERE idSucursal=:idSucursal";
        }
        try {
            $db = getConnection();
            $sentencia = $db->prepare($comando);
            $sentencia->bindParam('idSucursal', $postrequest->sucursal);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_OBJ);
            if ($resultado) {
                $arreglo = [
                    "estado" => 200,
                    "success"=>"OK",
                    "data" => $resultado
                ];
                return $response->withJson($arreglo,200);
            } else {
                $arreglo = [
                    "estado" => 400,
                    "error" => "Error al traer listado de Categoria",
                    "data" => $postrequest->sucursal
                ];;
                return $response->withJson($arreglo, 400);
            }
        } catch (PDOException $e) {
            $arreglo = [
                "estado" => 400,
                "error" => "Error al traer listado de Categoria",
                "datos" => $e
            ];
            return $response->withJson($arreglo, 400);//json_encode($wine);
        }

    }
}